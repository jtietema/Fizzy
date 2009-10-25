<?php
/**
 * Class Fizzy_Router
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

/** Fizzy_AutoFill */
require_once 'Fizzy/AutoFill.php';

/** Fizzy_Route_Default **/
require_once 'Fizzy/Route/Default.php';

/** Fizzy_Route_Regex **/
require_once 'Fizzy/Route/Regex.php';

/** Fizzy_Route_Simple **/
require_once 'Fizzy/Route/Simple.php';

/** Fizzy_Route_Static **/
require_once 'Fizzy/Route/Static.php';

/**
 * Router class for Fizzy.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Router extends Fizzy_AutoFill
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
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        parent::__construct($options);

        // Add default route
        array_unshift(
            $this->_routes,
            $this->_getRouteInstance(array(
                'type' => 'default',
                'controller' => 'default',
                'action' => 'default'
            ))
        );
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
     * Sets the routes for the router as an array. Routes will be converted to
     * Fizzy_Route classes. Any existent routes will be overridden.
     * @param array $routes
     * @return Fizzy_Router
     */
    public function setRoutes($routes)
    {
        // Reset the routes
        $this->_routes = array();

        // Add new routes
        $this->addRoutes($routes);
        
        return $this;
    }

    /**
     * Returns the routes for the router.
     * @return array
     */
    public function getRoutes()
    {
        return $this->_routes;
    }

    /**
     * Adds a route to the router. The route array will be converted to
     * a route class specified by the type attribute of the route.
     * @param string $name
     * @param array $config
     * @return Fizzy_Router
     */
    public function addRoute(array $config)
    {
        $this->_routes[] = $this->_getRouteInstance($config);

        return $this;
    }

    /**
     * Add multiple routes as array.
     * @param array $routes
     * @return Fizzy_Router
     */
    public function addRoutes(array $routes)
    {
        foreach($routes as $name => $config) {
            $this->addRoute($config);
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

        // No route found
        require_once 'Fizzy/Exception.php';
        throw new Fizzy_Exception('No route found.');
    }

    /**
     * Gets a route instance by config array.
     * @param array $config
     * @return object
     */
    protected function _getRouteInstance(array $config)
    {
        if(!isset($config['type']) || empty($config['type'])) {
            $config['type'] = 'Static';
        }

        $routeClass = 'Fizzy_Route_' . ucfirst($config['type']);
        $route = new $routeClass($this, $config);

        return $route;
    }
}
