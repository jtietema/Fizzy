<?php
/**
 * Abstract class Fizzy_ViewHelpers
 * @package Fizzy
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
 * Helper functions for Fizzy_View.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
abstract class Fizzy_ViewHelpers {

    /**
     * Returns an url with the base url prefixed.
     * @param string $url
     * @return string
     */
    public function url($url)
    {
        $request = $this->getRequest();
        
        return $this->_cleanUrl($request->getBaseUrl()) . $this->_cleanUrl($url);
    }

    /**
     * Creates an HTML link.
     * @param string $url
     * @param string $text
     * @param array $attributes
     * @return string
     */
    public function link($url, $text, array $attributes = array())
    {
        $html = '<a href="%s"%s>%s</a>';
        $url = $this->_completeUrl($url);
        
        $attributes = $this->_compileAttributes($attributes);

        return sprintf($html, $url, $attributes, $text);
    }

    /**
     * Creates an HTML image tag.
     * @param string $url
     * @param array $attributes
     * @return string
     */
    public function img($url, array $attributes = array())
    {
        $html = '<img src="%s"%s />';
        $url = $this->_completeUrl($url);
        $attributes = $this->_compileAttributes($attributes);
        
        return sprintf($html, $url, $attributes);
    }

    /**
     * Creates a complete url from the base url and the given url.
     * @param string $url
     */
    protected function _completeUrl($url)
    {
        $request = $this->_request;
        $baseUrl = $this->_cleanUrl($request->getBaseUrl());
        $url = $this->_cleanUrl($url);

        if(!empty($baseUrl) && 0 !== strpos($url, $baseUrl)) {
            $url = $baseUrl . $url;
        }

        return $url;
    }

    /**
     * Cleans an url making sure it starts with a slash and does
     * not end with a slash
     * @param string $url
     * @return string
     */
    protected function _cleanUrl($url)
    {
        if(0 !== strpos($url, DIRECTORY_SEPARATOR)) {
            $url = DIRECTORY_SEPARATOR . $url;
        }
        $url = rtrim($url, DIRECTORY_SEPARATOR);
        return $url;
    }

    /**
     * Compiles an array with key => value pairs into HTML attributes.
     * @param array $attributes
     * @return string
     */
    protected function _compileAttributes(array $attributes)
    {
        $html = '';
        foreach($attributes as $key => $value)
        {
            $html .= " {$key}=\"{$value}\"";
        }

        return $html;
    }

}