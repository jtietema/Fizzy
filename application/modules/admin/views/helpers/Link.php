<?php

class Zend_View_Helper_Link extends Zend_View_Helper_HtmlElement
{

    public function link($url, $text, array $attribs = array())
    {
        $xhtml = '<a href="%s" %s>%s</a>';

        return sprintf($xhtml, $this->view->baseUrl($url), $this->_htmlAttribs($attribs), $text);
    }
    
}