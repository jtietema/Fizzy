<?php
/**
 * Class Fizzy_Controller
 * @package Fizzy
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

/** Fizzy_Request */
require_once 'Fizzy/Request.php';

/**
 * Controller class for Fizzy.
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
     * Returns the request for this controller.
     * @return Fizzy_Request
     */
    public function getRequest()
    {
        return $this->_request;
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
     * Method is called before the request action is called on the controller 
     * class.
     */
    public function before()
    {}

    /**
     * Method is called after the request action is called on the controller
     * class.
     */
    public function after()
    {}

    /**
     * Returns all parameters passed to the controller.
     * @return array
     */
    protected function _getParams()
    {
        return $this->getRequest()->getParameters();
    }
    
    /**
     * Returns a parameter passed to the controller.
     * @param string $key
     * @return mixed
     */
    protected function _getParam($key, $default = null)
    {
        $parameters = $this->getRequest()->getParameters();
        if(!array_key_exists($key, $parameters)) {
            return $default;
        }

        return $parameters[$key];
    }

    /**
     * Redirect the request to another part of the application. Sets a Location
     * header and exits.
     * @param string $url
     */
    protected function _redirect($url)
    {
        header('Location: http://' . $this->_request->getServerName() . $this->_request->getBaseUrl() . $url);
        exit();
    }
}
