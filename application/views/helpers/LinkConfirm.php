<?php

class Zend_View_Helper_LinkConfirm extends Zend_View_Helper_HtmlElement
{

    public function linkConfirm($url, $text, $confirm, array $attribs = array())
    {
        $html = '<a href="%s" onClick="return confirm(\'%s\');"%s>%s</a>';

        $attributes = $this->_htmlAttribs($attribs);

        return sprintf($html, $url, $confirm, $attribs, $text);
    }
    
}