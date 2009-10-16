<?php
/**
 * Class Fizzy_Route_Default
 * @package Fizzy
 * @subpackage Route
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
        $path = $request->getPath();
        $pathParts = preg_split('/\//', $path, -1, PREG_SPLIT_NO_EMPTY);
        
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