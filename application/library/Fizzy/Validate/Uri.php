<?php
/**
 * Class Fizzy_Validate_Uri
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

/** Zend_Uri */
require_once 'Zend/Uri.php';

/**
 * Validator to check URI's
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Validate_Uri extends Zend_Validate_Abstract
{
    
    const INVALID_URI = 'invalidUri';

    /**
     * @see Zend_Validate_Abstract
     */
    protected $_messageTemplates = array(
        self::INVALID_URI   => "'%value%' is not a valid URI.",
    );

    /**
     * Checks if a URI is valid
     * @param string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $valueString = (string) $value;
        $this->_setValue($valueString);

        if (!Zend_Uri::check($value)) {
            $this->_error(self::INVALID_URI);
            return false;
        }
        return true;
    }

}