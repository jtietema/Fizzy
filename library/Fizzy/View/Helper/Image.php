<?php
/**
 * Class Fizzy_View_Helper_Image
 * @package Fizzy
 * @subpackage View
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
 * View helper for generating image tags.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_View_Helper_Image extends Zend_View_Helper_HtmlElement
{

    /**
     * Generates an html image tag. The path given is prefixed with the baseUrl
     * of the request. This can be ignored if prependBase is set to false in the
     * options array.
     * The options array is used to generate html attributes for the tag.
     * @param string $path
     * @param array $options
     * @return string
     */
    public function image($path, $options = array())
    {
        $prependBase = true;
        if (isset($options['prependBase'])) {
            $prependBase = (boolean) $options['prependBase'];
            unset($options['prependBase']);
        }

        if ($prependBase) {
            if (null == $this->view) {
                $this->view = new Zend_View();
            }
            $path = $this->view->baseUrl($path);
        }

        $xhtml = '<img src="' . $path . '"' . $this->_htmlAttribs($options) . ' />';

        return $xhtml;
    }
    
}