<?php

class Zend_View_Helper_Img extends Zend_View_Helper_HtmlElement
{

    public function img($image, array $attribs = array())
    {
        $xhtml = '<img %s src="%s" />';

        return sprintf($xhtml, $this->_htmlAttribs($attribs), $this->view->baseUrl($image));
    }
}
