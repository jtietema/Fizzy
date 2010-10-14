<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BlockOperations
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
class Fizzy_Decorator_BlockOperations extends Zend_Form_Decorator_Abstract
{
    protected $_format = '<div class="block">
        <div class="operations">
            <a href="%s/fizzy/pages/%s/deleteblock/%s"><img src="%s/fizzy_assets/images/icon/cross-small.png" alt="delete">
        </a></div>%s</div>';

    protected $_baseUrl = '';

    protected $_blockId = null;

    protected $_pageId = null;

    public function __construct($options = null)
    {
        if (isset($options['baseUrl'])) {
            $this->_baseUrl = $options['baseUrl'];
        }

        if (!isset($options['blockId'])) {
            throw new Fizzy_Exception('You are required to set a block id');
        }
        $this->_blockId = $options['blockId'];

        if (!isset($options['pageId'])) {
            throw new Fizzy_Exception('You are required to set a page id');
        }
        $this->_pageId = $options['pageId'];

        parent::__construct($options);
    }

    public function render($content)
    {
        $element = $this->getElement();

        $markup = sprintf($this->_format, $this->_baseUrl, $this->_pageId, $this->_blockId, $this->_baseUrl, $content);

        return $markup;
    }
}
