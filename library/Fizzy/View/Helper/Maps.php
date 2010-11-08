<?php
/**
 * Class Fizzy_View_Helper_Maps
 * @category Fizzy
 * @package Fizzy_View
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
 * Abstract class for extension
 */
require_once 'Zend/View/Helper/FormElement.php';


/**
 * Helper to generate a wysiwyg textarea element
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_View_Helper_Maps extends Zend_View_Helper_FormElement
{
    
    public function maps($name, $value = null, $attribs = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable

        // build the element
        $xhtml = '<div class="maps">'
                . '<div id="'.$this->view->escape($id).'" style="width: 500px; height: 500px;">'
                . '</div>'
                . '<input type="hidden" id="'.$this->view->escape($id).'-data" name="'.$name.'" value="'.$value.'" '.$this->getClosingBracket()
                . '<button onclick="fizzy.maps.addMarker(\''.$this->view->escape($id).'\'); return false;">Add Marker</button>'
                . '<script type="text/javascript">'
                . 'fizzy.maps.register("'.$this->view->escape($id).'");'
                . '</script>'
                . '</div>';

        return $xhtml;
    }
}
