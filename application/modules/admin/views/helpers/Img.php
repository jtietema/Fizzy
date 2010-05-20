<?php
/**
 * Class Admin_View_Helper_Img
 * @category Fizzy
 * @package Admin_View
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

class Zend_View_Helper_Img extends Zend_View_Helper_HtmlElement
{

    public function img($image, array $attribs = array())
    {
        $xhtml = '<img %s src="%s" />';

        return sprintf($xhtml, $this->_htmlAttribs($attribs), $this->view->baseUrl($image));
    }
}
