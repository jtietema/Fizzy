<?php
/**
 * Interface Fizzy_Route_Interface
 * 
 * @copyright Copyright (c) 2009 Voidwalkers (http://www.voidwalkers.nl)
 * @license http://opensource.org/licenses/mit-license.php The MIT License
 * @package Fizzy
 * @subpackage Route
 */

/**
 * Interface for route classes accepted by Fizzy_Router.
 * 
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
interface Fizzy_Route_Interface
{

    /**
     * Match the route against the request.
     * @param Fizzy_Request $request
     * @return boolean
     */
    public function match(Fizzy_Request $request);
    
}