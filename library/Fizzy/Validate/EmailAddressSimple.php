<?php
/**
 * Class Fizzy_Validate_EmailAddressSimple
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

/** Zend_Validate_Regex */
require_once 'Zend/Validate/Regex.php';

/**
 * Validates an email address by a regular expression. Will only return a simple
 * invalid message, not containing errors for TLD, domains, network addresses etc.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Validate_EmailAddressSimple extends Zend_Validate_Regex
{
    /**
     * @see Zend_ValidatE_Regex
     */
    protected $_pattern = '/^(([A-Za-z0-9]+_+)|([A-Za-z0-9]+\-+)|([A-Za-z0-9]+\.+)|([A-Za-z0-9]+\++))*[A-Za-z0-9]+@((\w+\-+)|(\w+\.))*\w{1,63}\.[a-zA-Z]{2,6}$/i';

    /**
     * @see Zend_Validate_Abstract
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID   => "Invalid type given, value should be string, integer or float",
        self::NOT_MATCH => "'%value%' is not a valid email address",
    );

    /**
     * Overwrite the constructor to make the pattern parameter optional.
     * @param string $pattern
     */
    public function __construct($pattern = null)
    {
        if (null !== $pattern) {
            parent::__construct($pattern);
        }
    }
    
}