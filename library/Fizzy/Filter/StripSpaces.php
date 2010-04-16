<?php
/**
 * Class Fizzy_Filter_StripSpaces
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
 * @copyright Copyright (c) 2009-2010 Voidwalkers (http://www.voidwalkers.nl)
 * @license http://www.voidwalkers.nl/license/new-bsd The New BSD License
 */

/** Zend_Filter_PregReplace */
require_once 'Zend/Filter/PregReplace.php';

/**
 * Filter to strip all spaces from a string.
 * 
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Filter_StripSpaces extends Zend_Filter_PregReplace
{
    /**
     * @see Zend_Filter_PregReplace
     */
    protected $_matchPattern = '(\s*)';
    
    /**
     * @see Zend_Filter_PregReplace
     */
    protected $_replacement = '';
}