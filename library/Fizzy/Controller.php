<?php
/**
 * Class Fizzy_Controller
 * 
 * @copyright Voidwalkers (http://www.voidwalkers.nl)
 * @license New BSD
 * @package Fizzy
 */

/** Fizzy_Request */
require_once 'Fizzy/Request.php';

/**
 * Controller class for Fizzy MVC framework.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Controller
{
    /**
     * The request object
     * @var Fizzy_Request
     */
    protected $_request = null;

    /**
     * View object.
     * @var Fizzy_View
     */
    protected $_view = null;

    /** **/
    
    /**
     * Initializes the controller with the request object.
     * @param Fizzy_Request $request
     */
    public function __construct(Fizzy_Request $request)
    {
        $this->_request = $request;
        $this->_init();
    }
    
    /**
     * Sets the view object to use for rendering.
     * @param Fizzy_View $view 
     * @return Fizzy_Controller
     */
    public function setView(Fizzy_View $view)
    {
        $this->_view = $view;

        return $this;
    }

    /**
     * Returns the view object.
     * @return Fizzy_View
     */
    public function getView()
    {
        return $this->_view;
    }

    /**
     * Allows subclasses to extend the initialization of the controller class.
     */
    protected function _init()
    {}
    
    /**
     * Returns all parameters passed to the controller.
     * @return array
     */
    protected function _getParams()
    {
        return $this->_request->getParameters();
    }
    
    /**
     * Returns a parameter passed to the controller.
     * @param string $key
     * @return mixed
     */
    protected function _getParam($key)
    {
        
    }
}
