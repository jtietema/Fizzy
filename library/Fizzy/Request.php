<?php
/**
 * Class Fizzy_Request
 * 
 * @copyright Voidwalkers (http://www.voidwalkers.nl)
 * @license New BSD
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
        
        $this->_parseParameters();
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
     * Returns all request parameters.
     * @return array
     */
    public function getParameters()
    {
        return $this->_parameters;
    }
    
    /**
     * Returns the requested controller.
     * @return string
     */
    public function getController()
    {
        return $this->_controller;
    }
    
    /**
     * Returns the requested action.
     * @return string
     */
    public function getAction()
    {
        return $this->_action;
    }
    
    /**
     * Parses the parameters from the path info and query string.
     */
    protected function _parseParameters()
    {
        $pathInfo = $this->_pathInfo;
        $pathParts = preg_split('/\//', $pathInfo, -1, PREG_SPLIT_NO_EMPTY);
        $controller = array_shift($pathParts);
        $action = array_shift($pathParts);
        
        if(empty($controller)) {
            $controller = 'default';
        }
        $this->_controller = $controller;
        if(empty($action)) {
            $action = 'default';
        }
        $this->_action = $action;
        
        // Parse query string
        $parameters = array();
        $pairs = explode('&', $this->_queryString);
        foreach($pairs as $pair) {
            list($key, $value) = explode('=', $pair);
            $parameters[$key] = (!is_null($value) ? $value : '');
        }
        
        $this->_parameters = $parameters;
    }

}
