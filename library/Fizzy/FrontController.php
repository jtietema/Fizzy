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
        $this->_config = $config;
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
     * Dispatches the request to a controller.
     * @param Fizzy_Request $request
     */
    public function dispatch()
    {
        $config = $this->_config;
        
        if(null === $this->_request) { $this->_request = new Fizzy_Request(); }
        $request = $this->_request;

        if(null === $this->_router) { $this->_router = new Fizzy_Router($this->_config->getConfiguration('routes')); }
        $router = $this->_router;

        // Find a route and inject the route parameters into the request object
        $router->route($request);
        
        // Get the controller and action
        $controller = $request->getController();

        // Check if controller exists
        $controllerClass = ucfirst($controller) . 'Controller';

        $controllerFileName = $controllerClass . '.php';
        $controllerFilePath = CONTROLLER_PATH . DIRECTORY_SEPARATOR . $controllerFileName;

        if(!is_file($controllerFilePath)) {
            require_once 'Fizzy/Exception.php';
            throw new Fizzy_Exception("Controller file for controller {$controllerClass} not found.");
        }
        require_once $controllerFilePath;
        
        if(!class_exists($controllerClass)) {
            require_once 'Fizzy/Exception.php';
            throw new Fizzy_Exception("Controller class {$controllerClass} not found.");
        }

        $reflectionClass = new ReflectionClass($controllerClass);
        $controllerInstance = $reflectionClass->newInstance($request);
        
        // Create a new view object for the controller
        $paths = $config->getConfiguration('paths');
        $view = new Fizzy_View();
        $view->setbasePath($paths['base'])
             ->setScriptPath($paths['view'])
             ->setLayoutPath($paths['layout']);
        $controllerInstance->setView($view);

        // retrieve the action
        $action = $request->getAction();
        $actionMethod = $action . 'Action';

        // set default view script based on controller and action names
        $viewScript = strtolower($controller) . DIRECTORY_SEPARATOR . strtolower($action) . '.phtml';
        $view->setScript($viewScript);

        // check if the action method exists
        if(!$reflectionClass->hasMethod($actionMethod)) {
            require_once 'Fizzy/Exception.php';
            throw new Fizzy_Exception("Action method {$actionMethod} in Controller {$controllerClass} not found.");
        }

        // call the action method
        $controllerInstance->$actionMethod();

        // Send the rendered output
        echo $view->render();
    }
    
}
