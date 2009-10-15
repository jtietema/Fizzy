<?php
/**
 * Class Fizzy_Route_Static
 * 
 * @copyright Copyright (c) 2009 Voidwalkers (http://www.voidwalkers.nl)
 * @license http://opensource.org/licenses/mit-license.php The MIT License
 * @package Fizzy
 * @subpackage Route
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
        $pathInfo = $request->getPathInfo();
        $rule = $this->getRule();

        // Make sure the rule starts with a /
        if(0 !== strpos('/', $rule)) {
            $rule = '/' . $rule;
        }

        if($pathInfo === $rule) {
            $request->setController($this->getController());
            $request->setAction($this->getAction());
            
            return true;
        }

        return false;
    }

}