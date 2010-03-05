<?php
/**
 * Class Fizzy_Image_Adapter_Abstract
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
 * Abstract class with base functionality for Fizzy_Image adapters.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
abstract class Fizzy_Image_Adapter_Abstract implements Fizzy_Image_Adapter_Interface
{
    /**
     * Image extensions supported by the adapter.
     * @var array
     */
    protected $_supportedExtensions = array();

    /** **/

    /**
     * Returns the extensions that are supported by the adapter.
     * @return string
     */
    public function getSupportedExtensions()
    {
        return $this->_supportedExtensions;
    }

    /**
     * Checks if an extension is supported by the adapter
     * @param string $extension
     * @return boolean
     */
    public function isSupportedExtension($extension)
    {
        return (boolean) in_array(strtolower($extension), $this->_supportedExtensions);
    }

    /**
     * Sets the extenions supported by the adapter.
     * @param array $extensions
     * @return Fizzy_Image_Adapter_Abstract
     */
    public function setSupportedExtensions(array $extensions)
    {
        $this->_supportedExtensions = $extensions;
        return $this;
    }

    /**
     * Returns the extension for a file path
     * @param string $path
     * @return string
     */
    public function _getExtension($path)
    {
        if(false === strrpos($path, '.')) {
            return '';
        }
        
        return substr($path, strrpos($path, '.') + 1);
    }

}