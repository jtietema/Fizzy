<?php

class Zend_View_Helper_Img extends Zend_View_Helper_HtmlElement
{

    public function img($image, array $attribs = array())
    {
        $xhtml = '<img ' . $this->_htmlAttribs($attribs) . ' src="' . $image . '" />';

        return $xhtml;
    }
}
