<?php
/**
 * Class Fizzy_Route_Static
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
 * Static route for Fizzy MVC. Will clear all parameters from the request.
 * 
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Route_Static extends Fizzy_Route_Abstract
{
    /**
     * Match the route against the request.
     * @param Fizzy_Request $request
     * @return boolean
     */
    public function match(Fizzy_Request $request)
    {
        $path = $request->getPath();
        $rule = $this->getRule();

        // Make sure the rule starts with a /
        if(0 !== strpos('/', $rule)) {
            $rule = '/' . $rule;
        }

        if($path === $rule) {
            $request->setController($this->_camelCase($this->getController()));
            $request->setAction($this->_camelCase($this->getAction()));
            
            return true;
        }

        return false;
    }

}