<?php

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

    public $_tinyMceConfig = array (
        'mode' => "exact",
        'elements' => "body",
        'theme' => "advanced",
        'theme_advanced_toolbar_location' => "top",
        'theme_advanced_toolbar_align' => "left",
        'theme_advanced_buttons1' => "formatselect,fontselect,fontsizeselect,separator,forecolor,backcolor,separator,removeformat,undo,redo",
        'theme_advanced_buttons2' => "bold,italic,underline,sub,sup,separator,justifyleft,justifycenter,justifyright,justifyfull,separator,bullist,numlist,separator,outdent,indent,blockquote,separator,link,unlink,image,separator,charmap,code,cleanup",
        'theme_advanced_buttons3' => "",
        'theme_advanced_statusbar_location' => "bottom",
        'theme_advanced_resizing' => true,
        'theme_advanced_resize_horizontal' => false
    );

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
     * @return string The element XHTML.
     */
    public function wysiwyg($name, $value = null, $attribs = null, $enabled = true)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable

        // is it disabled?
        $disabled = '';
        if ($disable) {
            // disabled.
            $disabled = ' disabled="disabled"';
        }

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
                . 'fizzy.wysiwyg.register("body");'
                . '</script>'
                . '</div>';
        
        return $xhtml;
    }
}
