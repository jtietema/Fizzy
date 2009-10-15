<?php
/**
 * Class Fizzy_Request
 * 
 * * @copyright Copyright (c) 2009 Voidwalkers (http://www.voidwalkers.nl)
 * @license http://opensource.org/licenses/mit-license.php The MIT License
 * @package Fizzy
 */

/**
 * Request class for Fizzy MVC framework.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Request
{
    /**
     * The protocol used.
     @var string
     */
    protected $_protocol = '';
    
    /**
     * The request method used: GET, POST, HEAD, PUT, DELETE.
     * @var string
     */
    protected $_method = '';
    
    protected $_requestUri = '';
    
    protected $_queryString = '';
    
    protected $_pathInfo = '';
    
    protected $_baseURL = '';
    
    /**
     * The request controller.
     * @var string
     */
    protected $_controller = '';
    
    /**
     * The requested action.
     * @var string
     */
    protected $_action = '';
    
    /**
     * The parameters passed in the request.
     * @var array
     */
    protected $_parameters = array();
    
    /** **/
    
    /**
     * Default constructor
     */
    public function __construct()
    {
        $this->_protocol = $_SERVER['SERVER_PROTOCOL'];
        $this->_method = $_SERVER['REQUEST_METHOD'];
        $this->_requestUri = $_SERVER['REQUEST_URI'];
        $this->_queryString = $_SERVER['QUERY_STRING'];
        $this->_pathInfo = (isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/');
        
        //$this->_parseParameters();
        // Get parameters
        $parameters = array();
        $pairs = preg_split('/&/', $this->_queryString, -1, PREG_SPLIT_NO_EMPTY);
        foreach($pairs as $pair) {
            list($key, $value) = preg_split('/=/', $pair, -1, PREG_SPLIT_NO_EMPTY);
            $parameters[$key] = (!is_null($value) ? $value : '');
        }

        $this->_parameters = $parameters;
    }
    
    /**
     * Returns the protocol used for the request.
     * @return string
     */
    public function getProtocol()
    {
        return $this->_protocol;
    }

    /**
     * Returns the used request method.
     * @return string
     */
    public function getMethod()
    {
        return $this->_method;
    }

    /**
     * Returns the path info.
     * @return string
     */
    public function getPathInfo()
    {
        return $this->_pathInfo;
    }

    /**
     * Returns all request parameters.
     * @return array
     */
    public function getParameters()
    {
        return $this->_parameters;
    }

    /**
     * Replaces the parameters with the new array
     * @param array $parameters
     * @return Fizzy_Request
     */
    public function setParameters(array $parameters)
    {
        $this->_parameters = $parameters;

        return $this;
    }

    /**
     * Adds a parameter to the request parameter list. Replaces any existing
     * parameter with that name.
     * @param string $name
     * @param mixed $value
     * @return Fizzy_Request
     */
    public function addParameter($name, $value)
    {
        $this->_parameters[$name] = $value;

        return $this;
    }

    /**
     * Add multiple parameters to the request parameter list as an array.
     * @param array $parameters
     * @return Fizzy_Request
     */
    public function addParameters(array $parameters)
    {
        foreach($parameters as $name => $value) {
            $this->addParameter($name, $value);
        }
        
        return $this;
    }
    
    /**
     * Returns the requested controller name.
     * @return string
     */
    public function getController()
    {
        return $this->_controller;
    }

    /**
     * Sets the controller name for the request.
     * @param string $controller
     * @return Fizzy_Request
     */
    public function setController($controller)
    {
        $this->_controller = $controller;

        return $this;
    }
    
    /**
     * Returns the requested action name.
     * @return string
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * Returns the action name for this request.
     * @param string $action
     * @return Fizzy_Request
     */
    public function setAction($action)
    {
        $this->_action = $action;

        return $this;
    }
    
    /**
     * Parses the parameters from the path info and query string.
     */
    protected function _parseParameters()
    {
        /*$pathInfo = $this->_pathInfo;
        $pathParts = preg_split('/\//', $pathInfo, -1, PREG_SPLIT_NO_EMPTY);*/

        /*$controller = array_shift($pathParts);
        $action = array_shift($pathParts);
        
        if(empty($controller)) {
            $controller = 'default';
        }
        $this->_controller = $controller;
        if(empty($action)) {
            $action = 'default';
        }
        $this->_action = $action;
        */
        // Parse query string
        $parameters = array();
        $pairs = preg_split('/&/', $this->_queryString, -1, PREG_SPLIT_NO_EMPTY);
        foreach($pairs as $pair) {
            list($key, $value) = preg_split('/=/', $pair, -1, PREG_SPLIT_NO_EMPTY);
            $parameters[$key] = (!is_null($value) ? $value : '');
        }
        
        $this->_parameters = $parameters;
    }

}
