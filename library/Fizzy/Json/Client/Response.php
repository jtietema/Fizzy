<?php
/**
 * Class Fizzy_Json_Client_Response
 * @category Fizzy
 * @package Fizzy_Json
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
 * @copyright Copyright (c) 2009-2010 Voidwalkers (http://www.voidwalkers.nl)
 * @license http://www.voidwalkers.nl/license/new-bsd The New BSD License
 */

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
