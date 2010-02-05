<?php
/**
 * Class Fizzy_Validate_PasswordConfirm
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
 * @copyright Copyright (c) 2009 Voidwalkers (http://www.voidwalkers.nl)
 * @license http://www.voidwalkers.nl/license/new-bsd The New BSD License
 */

/** Zend_Validate_Abstract */
require_once 'Zend/Validate/Abstract.php';

/**
 * Validator to check if a password field has the same value as another field
 * in the same form.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Validate_EqualsField extends Zend_Validate_Abstract
{
    const NOT_MATCH = 'notMatch';

    protected $_messageTemplates = array(
        self::NOT_MATCH => 'Password confirmation does not match'
    );

    /**
     * The opposite field name to check against.
     * @var string
     */
    protected $_oppositeField = 'password_confirm';

    /** **/

    /**
     * Set the field to check against.
     * @param string $field
     * @return Zend_Validate_Abstract
     */
    public function setOppositeField($field)
    {
        $this->_oppositeField = $field;

        return $this;
    }

    /**
     * Sets the fieldname shown in the error
     * @param string $name
     */
    public function setFieldName($name)
    {
        $this->setMessage($name . ' confirmation does not match', self::NOT_MATCH);
    }

    /**
     * Check if the field value is the same as the opposite field value.
     * @param string $value
     * @param mixed $context
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->_setValue($value);

        if(is_array($context)) {
            if(isset($context[$this->_oppositeField]) && ($value == $context[$this->_oppositeField])) {
                return true;
            }
        }
        else if (is_string($context) && ($value == $context)) {
            return true;
        }

        $this->_error(self::NOT_MATCH);
        return false;
    }
}