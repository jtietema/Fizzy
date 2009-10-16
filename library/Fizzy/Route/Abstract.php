<?php
/**
 * Abstract Class Fizzy_Route_Abstract
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
     * Sets the route rule.
     * @param string $rule
     * @return Fizzy_Route_Abstract
     */
    public function setRule($rule)
    {
        $this->_rule = $rule;

        return $this;
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
     * Sets the controller to route to.
     * @param string $controller
     * @return Fizzy_Route_Abstract
     */
    public function setController($controller)
    {
        $this->_controller = $controller;

        return $this;
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
     * Sets the action to route to.
     * @param string $action
     * @return Fizzy_Route_Abstract 
     */
    public function setAction($action)
    {
        $this->_action = $action;

        return $this;
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
