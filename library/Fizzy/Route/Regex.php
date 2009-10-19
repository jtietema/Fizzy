<?php
/**
 * Class Fizzy_Route_Regex
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
 * Route class to match request against a regular expression.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 * @todo Implement class
 */
class Fizzy_Route_Regex extends Fizzy_Route_Abstract
{
    /**
     * The named parameters from the assembled rule.
     * @var array
     */
    protected $_parameterMap = array();

    /** **/
    
    /**
     * Override of {@see Fizzy_Route_Abstract::__construct}.
     * @param Fizzy_Router $router
     * @param array $config
     */
    public function __construct(Fizzy_Router $router, array $config)
    {
        parent::__construct($router, $config);

        // Assemble the rule
        $this->assemble();
    }

    /**
     * Match the route against the request.
     * @param Fizzy_Request $request
     * @return boolean
     */
    public function match(Fizzy_Request $request)
    {
        return false;
    }

    /**
     * Assembles the rule and returns it.
     * @return string
     */
    public function assemble()
    {
        
    }

    
}