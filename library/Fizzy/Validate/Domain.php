<?php
/**
 * Class Fizzy_Validate_Domain
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

/** Zend_Validate_Abstract.php */
require_once 'Zend/Validate/Abstract.php';

/** Zend_Uri_Http */
require_once 'Zend/Uri/Http.php';

/**
 * Validates an URL to be a inside the Facebook domain.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Validate_Domain extends Zend_Validate_Abstract
{
    const INVALID = 'invalidURI';
    const OUTSIDE_DOMAIN = 'outsideDomain';

    protected $_messageTemplates = array(
        self::INVALID => "'%value%' is not a valid url",
        self::OUTSIDE_DOMAIN => "'%value%' is not within domain '%domain%",
    );

    /**
     * @var array
     */
    protected $_messageVariables = array(
        'domain' => '_domain'
    );

    /**
     * The valid domain
     * @var string
     */
    protected $_domain = '';

    /**
     * Wether to check the subdomain when validating the domain
     * @var boolean
     */
    protected $_validateSubdomain = false;

    /** **/

    /**
     * Validator constructor accepts a string or array with options. When a 
     * string is provided it is assumed this is the domain to validate against.
     * When an array is provided the array is searched for the keys 'domain' and 
     * 'validateSubdomain'.
     *
     * @param string|array $options
     */
    public function __construct($options)
    {
        if (is_string($options)) {
            $this->_domain = $options;
        }
        else if (is_array($options)) {

            if(isset($options['domain'])) {
                $this->_domain = $options['domain'];
            }

            if(isset($options['validateSubdomain'])) {
                $this->_validateSubdomain = (boolean) $options['validateSubdomain'];
            }
        }

        // Check if the domain is valid
        preg_match("{(?:http://|https://)?([^/]*)(?:.*)?}i", $this->_domain, $domainMatches);
        if (0 >= count($domainMatches)) {
            throw new Zend_Validate_Exception("Domain '$this->_domain' is not valid.");
        }

        $this->_domain = array_pop($domainMatches);
    }

    /**
     * Checks if a url is within the set domain and/or subdomain.
     * @param string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_setValue($value);

        try {
            $uri = Zend_Uri_Http::factory($value);
        } catch(Zend_Uri_Exception $e) {
            $this->_error(self::INVALID);
            return false;
        }

        // Explode the base and subdomains for our valid and input domains
        list($validBase, $validSub) = $this->_splitHost($this->_domain);
        list($inputBase, $inputSub) = $this->_splitHost($uri->getHost());

        if($validBase != $inputBase) {
            $this->_error(self::OUTSIDE_DOMAIN);
            return false;
        }

        // See if we need to validate the subdomain
        if($this->_validateSubdomain) {
            if($validSub != $inputSub) {
                $this->_error(self::OUTSIDE_DOMAIN);
                return false;
            }
        }

        return true;
    }

    /**
     * Splits the host into a base domain and subdomain. Returns an array where
     * the first key is the base domain and the second key is the subdomain.
     * @param string $host
     * @return array
     */
    protected function _splitHost($host)
    {
        preg_match("{(?:http://|https://)?([^/]*)(?:.*)?}i", $host, $hostMatches);
        
        $domainParts = explode('.', array_pop($hostMatches));
        $baseDomain = implode('.', array_splice($domainParts, -2));
        $subDomain = implode('.', $domainParts);

        return array($baseDomain, $subDomain);
    }

}
