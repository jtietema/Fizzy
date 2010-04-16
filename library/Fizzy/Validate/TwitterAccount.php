<?php
/**
 * Class Fizzy_Validate_TwitterAccount
 * @category Fizzy
 * @package Fizzy_Validate
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

/** Zend_Validate_Abstract */
require_once 'Zend/Validate/Abstract.php';

/** Zend_Uri_Http */
require_once 'Zend/Uri/Http.php';

/** Zend_Http_Client */
require_once 'Zend/Http/Client.php';

/**
 * Validate a Twitter account by a username. The account is checked by sending
 * an HTTP request to the profile page and checking the statuscode.
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
class Fizzy_Validate_TwitterAccount extends Zend_Validate_Abstract
{
    const BASEURI = 'http://twitter.com/';
    const INVALID_ACCOUNT = 'invalidAccount';

    protected $_messageTemplates = array(
        self::INVALID_ACCOUNT => "'%value%' is not a valid Twitter account."
    );

    /**
     * Checks if a username is a valid Twitter account by sending an HTTP
     * request to the profile page.
     * @param string $value
     * @return <type>
     */
    public function isValid($value)
    {
        $this->_setValue((string) $value);
        
        try {
            $uri = Zend_Uri_Http::factory(self::BASEURI . $value);
        } catch (Zend_Uri_Exception $e){
            $this->_error(self::INVALID_ACCOUNT);
            return false;
        }

        $httpClient = new Zend_Http_Client($uri);
        $response = $httpClient->request();

        if ($response->getStatus() != '200') {
            $this->_error(self::INVALID_ACCOUNT);
            return false;
        }
        
        return true;
    }
}
