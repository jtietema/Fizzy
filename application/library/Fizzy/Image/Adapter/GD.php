<?php
/**
 * Class Fizzy_Image_Adapter_GD
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
 * Fizzy_Image adapter using the GD library for image manipulation.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Image_Adapter_GD extends Fizzy_Image_Adapter_Abstract
{
    /**
     * The image resource the adapter works on
     * @var Resource
     */
    protected $_image = null;

    /** **/
    
    /**
     * @see Fizzy_Image_Adapter_Interface
     * @return Fizzy_Image_Adapter_Abstract
     */
    public function load($sourcePath)
    {
        if(!is_file($sourcePath)) {
            throw new Fizzy_Image_Exception("Path {$sourcePath} is not a file.");
        }
        else if(!is_readable($sourcePath)) {
            throw new Fizzy_Image_Exception("File {$sourcePath} could not be read,");
        }

        $imageData = file_get_contents($sourcePath);
        $this->_image = imagecreatefromstring($imageData);

        return $this;
    }

    /**
     * @see Fizzy_Image_Adapter_Interface
     * @return boolean
     */
    public function save($destination)
    {
        $destinationInfo = pathinfo($destination);
        $function = 'image' . $destinationInfo['extension'];
        if(!function_exists($function)) {
            throw new Fizzy_Image_Exception('Cannot create image of type ' . $extension);
        }

        $success = $function($this->_image, $destination);

        return $success;
    }

    /**
     * @see Fizzy_Image_Adapter_Interface
     * @return Fizzy_Image_Adapter_Abstract
     */
    public function resize($width, $height)
    {
        // Get the original image
        $originalImage = $this->_image;
        // Create a canvas for the resize image
        $targetImage = $this->_createCanvasFromImage($originalImage, $width, $height);
        imagefill($targetImage, 0, 0, imagecolorallocate($targetImage, 255, 255, 255));
        // Resize the image
        imagecopyresampled($targetImage, $originalImage, 0, 0, 0, 0, $width, $height, imagesx($originalImage), imagesy($originalImage));
        // Replace the original image with the resized image
        $this->_image = $targetImage;
        // Destroy the original canvas
        imagedestroy($originalImage);

        return $this;
    }

    /**
     * @see Fizzy_Image_Adapter_Interface
     * @return Fizzy_Image_Adapter_Adapter
     */
    public function crop($sourcex, $sourcey, $width, $height)
    {
        $originalImage = $this->_image;

        $targetImage = imagecreatetruecolor($width, $height);
        imagefill($targetImage, 0, 0, imagecolorallocate($targetImage, 255, 255, 255));

        imagecopyresampled($targetImage, $originalImage, 0, 0, $sourcex, $sourcey, $width, $height, $width, $height);

        $this->_image = $targetImage;

        imagedestroy($originalImage);

        return $this;
    }

    /**
     * @see Fizzy_Image_Adapter_Interface
     * @return int
     */
    public function getImageWidth()
    {
        return imagesx($this->_image);
    }

    /**
     * @see Fizzy_Image_Adapter_Interface
     * @return int
     */
    public function getImageHeight()
    {
        return imagesy($this->_image);
    }

    /**
     * Set the raw image resource.
     * @param Resource $image
     * @return Fizzy_Image_Adapter_GD
     */
    public function setRawImage($image)
    {
        $this->_image = $image;
        return $this;
    }

    /**
     * Returns the raw image resource.
     * @return Resource
     */
    public function getRawImage()
    {
        return $this->_image;
    }



    /**
     * Creates a canvas from an existing image transfering all properties to the
     * new resource. This optionally takes a width and height paramter for the
     * new canvas.
     * This will not copy the image data to the new canvas.
     * @param Resource $image
     * @param int $width
     * @param int $height
     * @param Resource $background
     * @return Resource
     */
    protected function _createCanvasFromImage($image, $width = null, $height = null, $background = null)
    {
         if(null === $width) {
             $width = imagesx($image);
         }
         if(null === $height) {
             $height = imagesy($image);
         }

        $canvas = null;
        if(imageistruecolor($image)) {
            $canvas = imagecreatetruecolor($width, $height);
            $transparentColorIndex = imagecolortransparent($image);
            if($transparentColorIndex >= 0) {
                imagecolortransparent($canvas, imagecolorsforindex($image, $transparentColorIndex));
            }
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
        } else {
            $canvas = imagecreate($width, $height);
        }

        if(null !== $background) {
            imagefill($canvas, 0, 0, $background);
        }

        return $canvas;
    }

    /**
     * Copy the image resource data to a new image resource.
     * @param Resource $image
     * @return Resource
     */
    protected function _copyImage($image)
    {
        if(null === $this->_image) {
            throw new Fizzy_Image_Exception('Cannot copy the contents of an empty image resource.');
        }

        // Turn the contents of the image resource into a string
        ob_start();
        imagegd2($this->_image);
        $imageData = ob_get_clean();

        // Create a new image resource from the data string
        $imageCopy = imagecreatefromstring($imageData);

        return $imageCopy;
    }

    public function __clone()
    {
        $this->_image = $this->_copyImage($this->_image);
    }

}