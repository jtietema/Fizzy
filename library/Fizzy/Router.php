<?php
/**
 * Class Fizzy_Router
 * 
 * * @copyright Copyright (c) 2009 Voidwalkers (http://www.voidwalkers.nl)
 * @license http://opensource.org/licenses/mit-license.php The MIT License
 * @package Fizzy
 */

/** Fizzy_Route_Static **/
require_once 'Fizzy/Route/Static.php';

/** Fizzy_Route_Regex **/
require_once 'Fizzy/Route/Regex.php';

/** Fizzy_Route_Default **/
require_once 'Fizzy/Route/Default.php';

/**
 * Router class for Fizzy MVC framework.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Router
{

    /**
     * Default controller name to route to.
     * @var string
     */
    protected $_defaultController = 'default';

    /**
     * Default action name to route to.
     * @var string
     */
    protected $_defaultAction = 'default';

    /**
     * The defined routes.
     * @var array
     */
    protected $_routes = array();

    /** **/

    /**
     * Fizzy_Router constructor.
     * @param array $routes
     */
    public function __construct(array $routes = array())
    {
        // Add default route
        $this->addRoute('default', array(
            'type' => 'default',
            'controller' => 'default',
            'action' => 'default'
        ));

        // Add routes from config
        $this->addRoutes($routes);
    }

    /**
     * Sets the default controller name to route to.
     * @param string $controller
     * @return Fizzy_Router
     */
    public function setDefaultController($controller)
    {
        $this->_defaultController = $controller;

        return $this;
    }

    /**
     * Returns the default controller name.
     * @return string
     */
    public function getDefaultController()
    {
        return $this->_defaultController;
    }

    /**
     * Sets the default action name to route to.
     * @param string $action
     * @return Fizzy_Router
     */
    public function setDefaultAction($action)
    {
        $this->_action = $action;

        return $this;
    }

    /**
     * Returns the default action name.
     * @return string
     */
    public function getDefaultAction()
    {
        return $this->_defaultAction;
    }

    /**
     * Adds a route to the router. The route array will be converted to
     * a route class specified by the type attribute of the route.
     * @param string $name
     * @param array $routeConfig
     * @return Fizzy_Router
     */
    public function addRoute($name, array $routeConfig)
    {
        if(!isset($routeConfig['type']) || empty($routeConfig['type'])) {
            $routeConfig['type'] = 'Default';
        }

        $routeClass = 'Fizzy_Route_' . ucfirst($routeConfig['type']);
        $route = new $routeClass($this, $routeConfig);

        $this->_routes[$name] = $route;

        return $this;
    }

    /**
     * Add multiple routes as array.
     * @param array $routes
     * @return Fizzy_Router
     */
    public function addRoutes(array $routes)
    {
        foreach($routes as $name => $route) {
            $this->addRoute($name, $route);
        }

        return $this;
    }

    /**
     * Tries to match the request to a route and inject the request with the
     * route information.
     * @param Fizzy_Request $request
     */
    public function route(Fizzy_Request $request)
    {
        foreach(array_reverse($this->_routes) as $route) {
            if($route->match($request)) {
                return $request; // Matching route found
            }
        }

        // If no matches were found, try a default route

        // No route found
        require_once 'Fizzy/Exception.php';
        throw new Fizzy_Exception('No route found.');
    }


}
