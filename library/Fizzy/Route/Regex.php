<?php
/**
 * Class Fizzy_Route_Regex
 * 
 * @copyright Voidwalkers (http://www.voidwalkers.nl)
 * @license New BSD
 * @package Fizzy
 * @subpackage Route
 */

/** Fizzy_Route_Abstract */
require_once 'Fizzy/Route/Abstract.php';

/**
 * Route class to match request against a regular expression.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
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