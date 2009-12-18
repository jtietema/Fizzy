<?php
/**
 * Class AdminAssetsController
 * @package Fizzy
 * @subpackage Admin
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

/** Fizzy_Controller */
require_once 'Fizzy/Controller.php';

/**
 * Controller for admin assets. Controls css, javascript and images.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class AdminAssetsController extends Fizzy_Controller
{

    public function serveAction()
    {
        $namespaces = array('css' => 'css', 'js' => 'js', 'image' => 'images', 'images' => 'images');
        $namespace = strtolower($this->_getParam('namespace'));
        
        // Disable view rendering
        $this->getView()->disable();
        
        // Check for valid namespace
        if(!array_key_exists($namespace, $namespaces))
        {
            $this->_return404();
            return;
        }

        // Get the absolute file path
        $asset = $this->_getAssetPath($this->getRequest()->getPath(), $namespaces[$namespace]);
        
        // Check if the asset is valid
        if(!$this->_isValidAsset($asset))
        {
            $this->_return404();
            return;
        }

        $contentType = $this->_getContentType($asset);
        if(null !== $contentType)
        {
            header('Content-Type: ' . $contentType);
        }
        echo file_get_contents($asset);
        return;
    }

    /**
     * Returns the absolute path for a requested asset.
     * @param string $url
     * @param string $assetType
     * @return string
     */
    protected function _getAssetPath($url, $assetType)
    {
        $assetUrl = '/admin/' . $assetType;
        $file = trim(str_replace($assetUrl, '', $url), DIRECTORY_SEPARATOR);
        
        $assetsPath = Fizzy_Config::getInstance()->getPath('assets');
        $assetsPath = rtrim($assetsPath, DIRECTORY_SEPARATOR);

        $filePath = $assetsPath . DIRECTORY_SEPARATOR . rtrim($assetType, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $file;
        return realpath($filePath);
    }

    /**
     * Checks if an asset file is valid. It checks if it exists and if it's
     * within the assets folder.
     * @param string $asset
     * @return boolean
     */
    protected function _isValidAsset($asset)
    {
        return (is_file($asset) && 0 === strpos(realpath($asset), realpath(Fizzy_Config::getInstance()->getPath('assets'))));
    }

    /**
     * Returns the content type for an asset file path. The mime-type is
     * only detemined by the file extension as the fileinfor PECL extension
     * can not be considered installed.
     * @param string $asset
     * @return string
     */
    protected function _getContentType($asset)
    {
        $extension = strtolower(substr(strrchr($asset, '.'), 1));

        $mimeTypes = array(
            'bmp' => 'image/bmp',
            'css' => 'text/css',
            'gif' => 'image/gif',
            'htm' => 'text/html',
            'html' => 'text/html',
            'ico' => 'image/x-icon',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'js' => 'application/x-javascript',
            'pdf' => 'application/pdf',
            'png' => 'image/png',
            'svg' => 'image/svg+xml',
            'txt' => 'text/plain',
        );

        if(array_key_exists($extension, $mimeTypes))
        {
            return $mimeTypes[$extension];
        }

        return null;
    }

    /**
     * Returns the response with a 404 header and exits.
     */
    protected function _return404()
    {
        header($this->getRequest()->getProtocol() . " 404 Not Found");
    }
    
}