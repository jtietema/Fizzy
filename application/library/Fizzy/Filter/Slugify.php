<?php
/**
 * Class Fizzy_Filter_Slugify
 * @category Fizzy
 * @package Fizzy_Filter
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

/** Zend_Filter_Interface */
require_once 'Zend/Filter/Interface.php';

/**
 * Filter to change a value to a slugified version. This replaces all non
 * letters and digits by a '-'.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Filter_Slugify implements Zend_Filter_Interface
{
    /**
     * Filters the value and returns a slugified version.
     * @param string $value
     */
    public function filter($value)
    {
        # Replace all non letters or digits by -
        $value = preg_replace('/\W+/', '-', $value);
        $value = strtolower(trim($value, '-'));
        
        return $value;
    }
}