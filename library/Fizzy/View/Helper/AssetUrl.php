<?php
/**
 * Class Fizzy_View_Helper_AssetUrl
 * @package Fizzy
 * @subpackage View
 * @category Helpers
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
 * @copyright 2010 Voidwalkers (http://www.voidwalkers.nl)
 * @license http://www.voidwalkers.nl/license/new-bsd The New BSD License
 */

/**
 * Helper to assist in loading fizzy assets in view files
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_View_Helper_AssetUrl extends Zend_View_Helper_Abstract
{

    /**
     * Generates an url to fizzy assets
     * @param string $asset the asset
     * @param boolean $prependBase if the base url should be added
     * @return string
     */
    public function assetUrl($asset, $prependBase = true)
    {
        $config = Zend_Registry::get('config');

        // Get the base for fizzy assets
        $base = (isset($config->assetsBase)) ? $config->assetsBase : 'fizzy_assets';
        $base = '/' . trim($base, '/');
        $base = rtrim($base, '/');

        // Clean the asset path
        $asset = '/' . trim($asset, '/');

        $url = $base . $asset;
        if ($prependBase) {
            // Check for View instance existence
            if (null == $this->view) {
                // Assume Fizzy view helpers are added to the view by default
                $this->view = new Zend_View();
            }
            // Prepend the base url
            $url = $this->view->baseUrl($url);
        }

        return $url;
    }
    
}