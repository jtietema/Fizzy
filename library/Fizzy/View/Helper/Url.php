<?php
/**
 * Class Fizzy_View_Helper_Url
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
 * View helper for generating urls
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_View_Helper_Url extends Zend_View_Helper_Abstract
{

    /**
     * Build an url.
     *
     * If the url starts with http:// it is assumed this is an external url
     * and the url is returned immediately.
     *
     * If the url starts with '@' followed by a route name the route is fetched
     * by that name and build with the parameters provided in a query string
     * notation.
     *
     * The options array can specify prependBase if the generated (internal)
     * url should container the base url. Zend_View_Helper_BaseUrl is used
     * for this.
     *
     * @param string $url
     * @param array $options
     * @return string
     */
    public function url($url, $options = array())
    {
        // If it starts with http:// we assume its an external link
        if (0 === strpos($url, 'http://')) {
            return $url;
        }
        
        $defaults = array('prependBase' => true);
        $options += $defaults;

        if (null === $this->view) {
            $view = new Zend_View();
        }

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

        return $url;
    }

}