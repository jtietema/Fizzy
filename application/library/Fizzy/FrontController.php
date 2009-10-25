<?php
/**
 * Class Fizzy_FrontController
 * @package Fizzy
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.voidwalkers.nl/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@voidwalkers.nl so we can send you a copy immediately.
 * 
 * @copyright Copyright (c) 2009 Voidwalkers (http://www.voidwalkers.nl)
 * @license http://www.voidwalkers.nl/license/new-bsd The New BSD License
 */

/** Fizzy_Request */
require_once 'Fizzy/Request.php';
/** Fizzy_Router */
require_once 'Fizzy/Router.php';
/** Fizzy_View */
require_once 'Fizzy/View.php';

/**
 * FrontController class for Fizzy. Dispatches the request to the correct 
 * Controller.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_FrontController
{
    /**
     * The application configuration.
     * @var Fizzy_Config
     */
    protected $_config = null;

    /**
     * The request object.
     * @var Fizzy_Request
     */
    protected $_request = null;
    
    /**
     * The router object.
     * @var Fizzy_Router
     */
    protected $_router = null;
    
    /** **/

    /**
     * Default constructor.
     * @param Fizzy_Config $config
     */
    public function __construct(Fizzy_Config $config = null)
    {
        // Check for path definitions
        $paths = $config->getSection('paths');
        if(!isset($paths['base'])) {
            require_once 'Fizzy/Exception.php';
            throw new Fizzy_Exception('No basepath configured.');
        }
        
        $this->_config = $config;
        // Register autoload function for model classes
        spl_autoload_register(array($this, 'autoloadModel'));
    }

    /**
     * Sets the application configuration.
     * @param Fizzy_Config $config 
     */
    public function setConfig(Fizzy_Config $config)
    {
        $this->_config = $config;
    }

    /**
     * Sets the request object.
     * @param Fizzy_Request $request
     */
    public function setRequest(Fizzy_Request $request)
    {
        $this->_request = $request;
    }
    
    /**
     * Sets the router object.
     * @param Fizzy_Router $router
     */
    public function setRouter(Fizzy_Router $router)
    {
        $this->_router = $router;
    }

    /**
     * Returns the full path to an application path.
     * @param string $alias
     * @return string|null
     */
    public function getFullPath($alias)
    {
        if(null === $this->_config->getPath($alias)) {
            return null;
        }
        $basePath = $this->_config->getPath('base');
        $path = $this->_config->getPath($alias);

        if(is_array($path)) {
            $pathsArray = array();
            foreach($path as $alias => $subpath) {
                $pathsArray[$alias] = implode(DIRECTORY_SEPARATOR, array($basePath, $subpath));
            }
            $path = $pathsArray;
        }
        else {
            $path = implode(DIRECTORY_SEPARATOR, array($basePath, $path));
        }
        
        return $path;
    }

    /**
     * Dispatches the request to a controller.
     * @param Fizzy_Request $request
     */
    public function dispatch()
    {
        $config = $this->_config;
        $application = $config->getSection('application');
        
        if(null === $this->_request) {
            $this->_request = new Fizzy_Request();
            if(isset($application['baseUrl'])) {
                $this->_request->setBaseUrl($application['baseUrl']);
            }
        }
        $request = $this->_request;
        
        if(null === $this->_router) {
            $this->_router = new Fizzy_Router($config->getSection('routes'));
        }
        $router = $this->_router;

        // Find a route and inject the route parameters into the request object
        $router->route($request);
        
        // Get the controller and action
        $controller = $request->getController();

        // Check if controller exists
        $controllerClass = $controller . 'Controller';

        $controllerFileName = ucfirst($controllerClass) . '.php';
        $controllerFilePath = $this->getFullPath('controllers') . DIRECTORY_SEPARATOR . $controllerFileName;

        if(!is_file($controllerFilePath)) {
            require_once 'Fizzy/Exception.php';
            throw new Fizzy_Exception("Controller file for {$controllerClass} not found under controller directory {$controllerFilePath}.");
        }
        require_once $controllerFilePath;
        
        if(!class_exists($controllerClass)) {
            require_once 'Fizzy/Exception.php';
            throw new Fizzy_Exception("Controller class {$controllerClass} not found.");
        }

        $reflectionClass = new ReflectionClass($controllerClass);
        $controllerInstance = $reflectionClass->newInstance($request);
        
        // Create a new view object for the controller
        $view = new Fizzy_View();
        $view->setScriptPaths($this->getFullPath('views'))
             ->setLayoutPaths($this->getFullPath('layouts'));
        $controllerInstance->setView($view);

        // retrieve the action
        $action = $request->getAction();
        $actionName = strtolower(substr($action, 0, 1)) . substr($action, 1, strlen($action));
        $actionMethod = $actionName . 'Action';
        
        // set default view script based on controller and action names
        $viewScript = strtolower($controller) . DIRECTORY_SEPARATOR . strtolower($action) . '.phtml';
        $view->setScript($viewScript);

        // check if the action method exists
        if(!$reflectionClass->hasMethod($actionMethod)) {
            require_once 'Fizzy/Exception.php';
            throw new Fizzy_Exception("Action method {$actionMethod} in Controller {$controllerClass} not found.");
        }

        // Call the before method
        $controllerInstance->before();
        // call the action method
        $controllerInstance->$actionMethod();
        // Call the after method
        $controllerInstance->after();

        // Check if the view should be rendered
        if($view->isEnabled()) {

            // Get the output from the action view
            $viewOuput = $view->render();

            $layout = $view->getLayout();
            if(empty($layout)) {
                // No layout specified, send the view output as a response
                echo $viewOuput;
            }
            else {
                $layout = new Fizzy_View();
                $layout->setScriptPaths($view->getLayoutPaths())
                       ->setScript($view->getLayout());

                $layout->assign('content', $viewOuput);
                echo $layout->render();
            }
        }
    }

    /**
     * Autoload function for model classes.
     * @param string $class
     */
    public static function autoloadModel($class)
    {
        $config = Fizzy_Config::getInstance();
        $modelFile = ucfirst($class) . '.php';
        $modelDirectory = $config->getPath('base') . DIRECTORY_SEPARATOR . $config->getPath('models');

        $modelsPath = $modelDirectory . DIRECTORY_SEPARATOR . $modelFile;
        if(is_file($modelsPath)) {
            require $modelsPath;
            return true;
        }

        return false;
    }
    
}
