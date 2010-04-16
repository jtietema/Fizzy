<?php
/**
 * This class parses a Zend_Http_Response containing a JSON-RPC response.
 *
 * @author jeroen
 * @todo Should this extend Zend_Json_Response ???
 */
class Fizzy_Json_Client_Response {

    protected $_error = null;
    protected $_id = null;
    protected $_result = null;

    public function __construct(Zend_Http_Response $response)
    {
        $body = $response->getRawBody();
        $json = Zend_Json::decode($body);

        $this->_id = $json['id'];

        if (null !== $json['error']){
            $this->_error = $json['error'];
        } else {
            $this->_result = $json['result'];
        }
    }

    public function getId()
    {
        return $this->_id;
    }

    public function getResult()
    {
        return $this->_result;
    }

    public function getError()
    {
        return $this->_error;
    }

    public function isError()
    {
        return (null !== $this->_error);
    }

}
