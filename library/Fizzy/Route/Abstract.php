<?php
/**
 * Abstract Class Fizzy_Route_Abstract
 * 
 * @copyright Copyright (c) 2009 Voidwalkers (http://www.voidwalkers.nl)
 * @license http://opensource.org/licenses/mit-license.php The MIT License
 * @package Fizzy
 * @subpackage Route
 */

/** Fizzy_Route_Interface */
require_once 'Fizzy/Route/Interface.php';

/**
 * Abstract route class.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
abstract class Fizzy_Route_Abstract implements Fizzy_Route_Interface
{
    
    /**
     * The router object.
     * @var Fizzy_Router
     */
    protected $_router = null;

    /**
     * Route rule to match against.
     * @var string
     */
    protected $_rule = '';

    /**
     * Controller name to route to.
     * @var string
     */
    protected $_controller = '';

    /**
     * Action name to route to.
     * @var string
     */
    protected $_action = '';

    /** **/

    /**
     * Route constructor. Takes an array with configuration options.
     * @param array $config
     */
    public function __construct(Fizzy_Router $router, array $config)
    {
        $this->setRouter($router);
        
        $this->_rule = $config['rule'];
        $this->_controller = $config['controller'];
        $this->_action = $config['action'];
    }

    /**
     * Sets the router object.
     * @param Fizzy_Router $router
     * @return Fizzy_Route
     */
    public function setRouter(Fizzy_Router $router)
    {
        $this->_router = $router;

        return $this;
    }

    /**
     * Returns the router object.
     * @return Fizzy_Router
     */
    public function getRouter()
    {
        return $this->_router;
    }

    /**
     * Returns the rule for this route.
     * @return string
     */
    public function getRule()
    {
        return $this->_rule;
    }

    /**
     * Returns the controller this route routes to.
     * @return string
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * Returns the action this route routes to.
     * @return string
     */
    public function getAction()
    {
        return $this->_action;
    }

}
