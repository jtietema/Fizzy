<?php
/**
 * Class Fizzy_View_Helper_Wysiwyg
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
 * @copyright Copyright (c) 2009 Voidwalkers (http://www.voidwalkers.nl)
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
class Fizzy_View_Helper_Wysiwyg extends Zend_View_Helper_FormElement
{
    /**
     * The default number of rows for a textarea.
     *
     * @access public
     *
     * @var int
     */
    public $rows = 24;

    /**
     * The default number of columns for a textarea.
     *
     * @access public
     *
     * @var int
     */
    public $cols = 80;

    /**
     * Generates a 'textarea' element.
     *
     * @access public
     *
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     *
     * @param mixed $value The element value.
     *
     * @param array $attribs Attributes for the element tag.
     *
     * @param boolean
     *
     * @return string The element XHTML.
     */
    public function wysiwyg($name, $value = null, $attribs = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable

        // is it disabled?
        $disabled = '';
        /*
        if ($enabled) {
            // disabled.
            $disabled = ' disabled="disabled"';
        }*/

        // Make sure that there are 'rows' and 'cols' values
        // as required by the spec.  noted by Orjan Persson.
        if (empty($attribs['rows'])) {
            $attribs['rows'] = (int) $this->rows;
        }
        if (empty($attribs['cols'])) {
            $attribs['cols'] = (int) $this->cols;
        }

        // build the element
        $xhtml = '<div class="wysiwyg">'
                . '<div class="wysiwyg-editor">'
                . '<textarea name="' . $this->view->escape($name) . '"'
                . ' id="' . $this->view->escape($id) . '"'
                . $disabled
                . $this->_htmlAttribs($attribs) . '>'
                . $this->view->escape($value) . '</textarea>'
                . '</div>'
                . '<div class="wysiwyg-toggle"></div>'
                . '<script type="text/javascript">'
                . 'fizzy.wysiwyg.register("'.$this->view->escape($id).'");'
                . '</script>'
                . '</div>';
        
        return $xhtml;
    }
}
