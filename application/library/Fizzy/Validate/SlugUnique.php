<?php
/**
 * Class Fizzy_Validate_SlugUnique
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
 * Form validator to check if a page slug is unique. Advises the Fizzy_Storage
 * to check this.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Validate_SlugUnique extends Zend_Validate_Abstract
{
    const NOT_UNIQUE = 'notUnique';

    protected $_messageTemplates = array (
        self::NOT_UNIQUE => 'Slug is not unique.'
    );

    /** **/

    /**
     * Check if a username is valid (unique).
     * @param string $value
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $value = (string) $value;
        $this->_setValue($value);
        
        $query = Doctrine_Query::create()->from('Page')->where('slug = ?', $value);
        $pages = $query->fetchArray();

        // we already have multiple items with this slug (should never occur).
        if (count($pages) > 1){
            $this->_error(self::NOT_UNIQUE);
            return false;
        }
        
        // check if the found slug belongs to the current document. If not return false.
        if (count($pages) === 1 && $context['id'] !== $pages[0]['id']) {
            $this->_error(self::NOT_UNIQUE);
            return false;
        }

        return true;
    }
}
