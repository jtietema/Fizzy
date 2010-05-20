<?php
/**
 * Class Fizzy_Image_Adapter_ImageMagick
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
 * @copyright Copyright (c) 2009-2010 Voidwalkers (http://www.voidwalkers.nl)
 * @license http://www.voidwalkers.nl/license/new-bsd The New BSD License
 */

/**
 * Fizzy_Image adapter for ImageMagick.
 */
class Fizzy_Image_Adapter_ImageMagick extends Fizzy_Image_Adapter_Abstract
{

    /**
     * Raw iamge object
     * @var Imagick
     */
    protected $_image = null;

    /** **/
    
    /**
     * @see Fizzy_Image_Adapter_Interface
     */
    public function load($sourcePath)
    {
        if(!is_file($sourcePath)) {
            throw new Fizzy_Image_Exception("Path {$sourcePath} is not a file.");
        }
        else if(!is_readable($sourcePath)) {
            throw new Fizzy_Image_Exception("File {$sourcePath} could not be read,");
        }

        $this->_image = new Imagick($sourcePath);

        return $this;
    }

    /**
     * @see Fizzy_Image_Adapter_Interface
     */
    public function save($destination)
    {
        //if(!is_writable($destination)) {
        //    throw new Fizzy_Image_Exception("Destination '{$destination}' is not writable.");
        //}
        
        $this->_image->writeImage($destination);

        return $this;
    }

    /**
     * @see Fizzy_Image_Adapter_Interface
     */
    public function getImageWidth()
    {
        return $this->_image->getImageWidth();
    }

    /**
     * @see Fizzy_Image_Adapter_Interface
     */
    public function getImageHeight()
    {
        return $this->_image->getImageHeight();
    }

    /**
     * @see Fizzy_Image_Adapter_Interface
     */
    public function resize($width, $height)
    {
        $original = $this->_image;
        $image = $original->clone();
        $image->setImageOpacity(1.0);

        $image->resizeImage($width, $height, Imagick::FILTER_CUBIC, 0);

        $original->destroy();
        $this->_image = $image;
    }

    /**
     * @see Fizzy_Image_Adapter_Interface
     */
    public function crop($startx, $starty, $width, $height)
    {
        
    }
    
}