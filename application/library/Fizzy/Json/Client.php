<?php
require_once 'Zend/Json.php';
require_once 'Zend/Http/Client.php';

require_once 'Fizzy/Json/Client/Exception.php';
require_once 'Fizzy/Json/Client/Response.php';

/**
 * This class makes talking to Zend_Json_Server (or other JSON-RPC servers) a
 * breeze. It wraps the Zend_Http_Client en only exposes some convenience
 * methods.
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
class Fizzy_Json_Client
{

    /**
     * The url of the Json server.
     */
    protected $_url = null;
    
    /**
     * The id is copied from the request to the response on the server and can
     * be used to identify specific responses when you are handeling multiple
     * to the same method.
     * @var mixed
     */
    protected $_id = null;

    /**
     * The method to be called on the JSON-RPC server. Eg. 'hellworld'. When using
     * namespaces the namespace is prepend with a dot on the end. E.g.
     * 'helloworldnamespace.sayhello'
     * @var string
     */
    protected $_method = null;

    /**
     * A list of parameters passed to the method.
     * @var array
     */
    protected $_params = array();

    /**
     * Cached instance of Zend_Http_Client
     * @var Zend_Http_Client|null
     */
    protected $_httpClient = null;

    /**
     * Array of strings with methodnames support by the __call magic method.
     * This allows for a more native API feel.
     * @var array
     */
    protected $_allowedMethods = array();

    /**
     * Returns the Url of the JSON-RPC server
     * 
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * Set the Url of the JSON-RPC server.
     * 
     * @param string $url
     * @return Fizzy_Json_Client
     */
    public function setUrl($url)
    {
        $this->_url = $url;
        return $this;
    }

    /**
     * Returns the Id give to the next method call.
     * @return mixed
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set the id for the next request to the JSON-RPC server. You can use this
     * id to match request en response.
     * 
     * @param mixed $id
     * @return Fizzy_Json_Client
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    /**
     * Set the method to be called on the JSON-RPC server.
     * 
     * @param string $method
     * @return Fizzy_Json_Client
     */
    public function setMethod($method)
    {
        $this->_method = $method;
        return $this;
    }

    /**
     * Returns the parameters given to the method.
     * 
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Sets the parameters to the method called on the JSON-RPC server. Make sure
     * all the parameters are convertable to Json format.
     *
     * @param array $params
     * @return Fizzy_Json_Client
     */
    public function setParams(Array $params)
    {
        $this->_params = $params;
        return $this;
    }

    /**
     * Returns the current http object.
     * 
     * @return Zend_Http_Client|null
     */
    public function getHttpClient()
    {
        return $this->_httpClient;
    }

    /**
     * Allows to set your own instance of the http client used for making requests
     * 
     * @param Zend_Http_Client $httpClient
     * @return Fizzy_Json_Client
     */
    public function setHttpClient(Zend_Http_Client $httpClient)
    {
        $this->_httpClient = $httpClient;
        return $this;
    }

    /**
     * Make the request to the JSON-RPC server.
     *
     * @return Fizzy_Json_Client_Response
     */
    public function request()
    {
        if ($this->_httpClient === null || !($this->_httpClient instanceof Zend_Http_Client)) {

            if ($this->_url === null) {
                throw new Fizzy_Json_Client_Exception('No Url set.');
            }

            if (empty($this->_method)) {
                throw new Fizzy_Json_Client_Exception('No method set.');
            }

            $this->_httpClient = new Zend_Http_Client();
            $this->_httpClient->setMethod(Zend_Http_Client::POST);

        }

        $this->_httpClient->setUri($this->_url);
        
        $json = array(
            'id' => $this->_id,
            'method' => $this->_method,
            'params' => $this->_params
        );

        $this->_httpClient->setRawData(Zend_Json::encode($json));
        $rawResponse = $this->_httpClient->request();

        $response = new Fizzy_Json_Client_Response($rawResponse);
        return $response;
    }

    /**
     * All allowed methods for using with __call
     * 
     * @return <type>
     */
    public function getAllowedMethods()
    {
        return $this->_allowedMethods;
    }

    /**
     * Sets all allowed methods for use with __call. This allows you to call
     * remote methods in a native like way.
     * 
     * @param array $methods
     */
    public function setAllowedMethods(Array $methods)
    {
        $this->_allowedMethods = $methods;
    }

    /**
     * Proxies all calls to the webserver when the methods are in allowedMethods array
     * 
     * @param string $name
     * @param array $args
     * @return Fizzy_Json_Client_Response
     */
    public function __call($name, $args)
    {
        if (!in_array($name, $this->_allowedMethods)){
            throw new Fizzy_Json_Client_Exception(
                'Method not supported. Did you set it with setAllowedMethods?'
            );
        }
        $this->setMethod($name);
        $this->setParams($args);
        /**
         * @todo Return the Response object or the result??
         */
        return $this->request();
    }

}
