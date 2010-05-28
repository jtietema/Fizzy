<?php
/**
 * Class Fizzy_View_Helper_Link
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
 * View helper for generating a link tag.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_View_Helper_Link extends Zend_View_Helper_HtmlElement
{

    /**
     * Construct a link tag. The passed url is prepended with the base url. This
     * can be ignore by setting prependBase to false in the options.
     *
     * A javascript confirm dialog can be added to the link by specifying the
     * confirm key in the options array. This text is converted to a javascript
     * confirm dialog.
     *
     * Output escaping can be turned of by setting the escape options to false
     * in the options array.
     * 
     * @param string $url
     * @param string $value
     * @param array $options
     * @return string
     */
    public function link($url, $value, $options = array())
    {
        $defaults = array('prependBase' => true, 'escape' => true);
        $options += $defaults;

        if (null === $this->view) {
            $view = new Zend_View();
        }

        $escape = (boolean) $options['escape'];
        // Remove the escape option so it doesn't appear as an html attribute
        unset($options['escape']);

        // Check if the url was passed as a route name
        if (0 === strpos($url, '@')) {

            $routeName = substr($url, 1);
            $params = array();

            // Check for route parameters
            if (false !== strpos($routeName, '?')) {
                list($routeName, $paramString) = explode('?', $routeName);
                if (empty($paramString)) {
                    break;
                }
                
                $paramPairs = explode('&', $paramString);
                foreach ($paramPairs as $pair) {
                    if (false !== strpos($pair, '=')) {
                        list($pairKey, $pairValue) = explode('=', $pair);
                        $params[$pairKey] = $pairValue;
                    } else {
                        $params[$pairKey] = null;
                    }
                }
            }

            $router = Zend_Controller_Front::getInstance()->getRouter();
            $route = $router->getRoute($routeName);

            // Build the url with route and parameters
            $url = $route->assemble($params);
        }

        // Add base url if prependBase is true
        if ((boolean) $options['prependBase']) {
            $url = $this->view->baseUrl($url);
        }
        // Remove the prependBase option so it doesn't appear as an html attribute
        unset($options['prependBase']);

        // Check for confirm option
        if (isset($options['confirm'])) {
            $confirmText = $options['confirm'];
            $confirm = "return confirm('{$this->view->escape($confirmText)}');";
            unset($options['confirm']);

            $onclick = '';
            if (isset($options['onClick'])) {
                $onclick = $options['onClick'];
                unset($options['onClick']);
            } else if (isset($options['onclick'])) {
                $onclick = $options['onclick'];
                unset($options['onclick']);
            }

            if (empty($onclick)) {
                $onclick = $confirm;
            }
            else {
                $onclick = $confirm . $onclick;
            }

            $options['onClick'] = $confirm;
        }

        // Construct the tag
        $xhtml = '<a href="' . $url . '"' . $this->_htmlAttribs($options) . '>';
        $xhtml .= ($escape) ? $this->view->escape($value) : $value;
        $xhtml .= '</a>';

        return $xhtml;
    }
    
}