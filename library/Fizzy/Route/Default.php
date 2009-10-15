<?php
/**
 * Class Fizzy_Route_Auto
 * 
 * @copyright Copyright (c) 2009 Voidwalkers (http://www.voidwalkers.nl)
 * @license http://opensource.org/licenses/mit-license.php The MIT License
 * @package Fizzy
 * @subpackage Route
 */

/** Fizzy_Route_Abstract */
require_once 'Fizzy/Route/Abstract.php';

/**
 * Default route for Fizzy MVC framework. Will match controller and action
 * against the first two parameters in PATH_INFO. If no controller or action
 * was specified the defaults from Fizzy_Router will be used.
 *
 * Remaining parts of the route will be added to the request as parameters.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Route_Default extends Fizzy_Route_Abstract
{
    /**
     * Match the route against the request.
     * @param Fizzy_Request $request
     * @return boolean
     */
    public function match(Fizzy_Request $request)
    {
        $pathInfo = $request->getPathInfo();
        $pathParts = preg_split('/\//', $pathInfo, -1, PREG_SPLIT_NO_EMPTY);
        
        // Get the controller and action from the path parts
        $controller = array_shift($pathParts);
        $action = array_shift($pathParts);

        // Substitute empty controller or action
        if(empty($controller)) {
            $controller = $this->_router->getDefaultController();
        }
        if(empty($action)) {
            $action = $this->_router->getDefaultAction();
        }

        // Convert the rest of the parts to parameter pairs
        $parameters = array();
        while(count($pathParts) > 0) {
            $parameters[array_shift($pathParts)] = array_shift($pathParts);
        }

        $request->setController($controller);
        $request->setAction($action);
        $request->addParameters($parameters);

        return true;
    }
}