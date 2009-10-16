<?php
/**
 * Class Fizzy_Request
 * 
 * @copyright Copyright (c) 2009 Voidwalkers (http://www.voidwalkers.nl)
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

    const METHOD_GET    = 'GET';
    const METHOD_POST   = 'POST';
    const METHOD_DELETE = 'DELETE';
    const METHOD_PUT    = 'PUT';
    const METHOD_HEAD   = 'HEAD';

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

    /**
     * The complete requested URI.
     * @var string
     */
    protected $_requestUri = '';

    /**
     * The server name.
     * @var string
     */
    protected $_serverName = '';

    /**
     * The requested path within the application.
     * @var string
     */
    protected $_path = '';

    /**
     * The query string passed in the request.
     * @var string
     */
    protected $_queryString = '';

    /**
     * The base URL for the request.
     * @var string
     */
    protected $_baseUrl = '';
    
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
        $this->_method = strtoupper($_SERVER['REQUEST_METHOD']);
        $this->_requestUri = $_SERVER['REQUEST_URI'];
        $this->_serverName = $_SERVER['SERVER_NAME'];
        $this->_path = (isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/');
        $this->_queryString = $_SERVER['QUERY_STRING'];
        
        // Parse query string parameters
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
     * Returns the complete requested URI.
     * @return string
     */
    public function getRequestUri()
    {
        return $this->_requestUri;
    }

    /**
     * Returns the server name.
     * @return string
     */
    public function getServerName()
    {
        return $this->_serverName;
    }

    /**
     * Returns the path info.
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * Returns the query string from the request.
     * @return string
     */
    public function getQueryString()
    {
        return $this->_queryString;
    }

    /**
     * Sets the base URL for the request.
     * @param string $baseUrl
     * @return Fizzy_Request
     */
    public function setBaseUrl($baseUrl) {
        $this->_baseUrl = $baseUrl;

        return $this;
    }

    /**
     * Returns the base URL for the request.
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }

    /**
     * Returns all request parameters as key => value pairs. This contains the
     * parameters from the query string and parameters injected by routes.
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
    
}
