<?php
/**
 * Class Fizzy_FrontController
 *
 * @copyright Copyright (c) 2009 Voidwalkers (http://www.voidwalkers.nl)
 * @license http://opensource.org/licenses/mit-license.php The MIT License
 * @package Fizzy
 */

/** Fizzy_Request */
require_once 'Fizzy/Request.php';
/** Fizzy_Router */
require_once 'Fizzy/Router.php';

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
        if(null === $this->_request) { $this->_request = new Fizzy_Request(); }
        $request = $this->_request;

        if(null === $this->_router) { $this->_router = new Fizzy_Router($this->_config->getConfiguration('routes')); }
        $router = $this->_router;

        // Find a route and inject the route parameters into the request object
        $router->route($request);
        
        // Get the controller and action
        $controller = $request->getController();
        $action = $request->getAction();

        // Dispatch the action
        
        // Check if controller exists
        $controllerClass = ucfirst($controller) . 'Controller';
        $actionMethod = $action . 'Action';

        $controllerFile = CONTROLLER_PATH . '/' . $controllerClass . '.php';
        if(!is_file($controllerFile)) {
            require_once 'Fizzy/Exception.php';
            throw new Fizzy_Exception("Controller file for controller {$controllerClass} not found.");
        }

        include_once $controllerFile;
        if(!class_exists($controllerClass)) {
            require_once 'Fizzy/Exception.php';
            throw new Fizzy_Exception("Controller class {$controllerClass} not found.");
        }

        $controllerInstance = new $controllerClass($request);
        $controllerInstance->$actionMethod();
    }
    
}
