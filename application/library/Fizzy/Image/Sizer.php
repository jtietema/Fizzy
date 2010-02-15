<?php
/**
 * Class Fizzy_Image_Sizer
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
 * Class for creating multiple sizes of an image.
 * 
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Image_Sizer
{

    /**
     * Original image object.
     * @var Fizzy_Image
     */
    protected $_image = null;

    /**
     * Target path where resized images will be stored.
     * @var string
     */
    protected $_targetPath = '';

    /**
     * Type of the resized images.
     * @var string
     */
    protected $_imageType = 'png';

    /**
     * Name template for resized images.
     * @var string
     */
    protected $_nameTemplate = 'resized-%width%x%height%.%type%';

    /** **/

    /**
     * Constructor.
     * @param Fizzy_Image $image
     */
    public function __construct(Fizzy_Image $image)
    {
        $this->setImage($image);
    }

    /**
     * Set the base image.
     * @param Fizzy_Image $image
     * @return Fizzy_Image_Sizer
     */
    public function setImage(Fizzy_Image $image)
    {
        $this->_image = $image;
        return $this;
    }

    /**
     * Set the type for the resized images.
     * @param string $type
     * @return Fizzy_ImagE_Sizer
     */
    public function setImageType($type)
    {
        $this->_imageType = $type;

        return $this;
    }

    public function setNameTemplate($template)
    {
        $this->_nameTemplate = $template;

        return $this;
    }

    public function setTargetPath($path)
    {
        $realpath = realpath($path);
        if(false === $realpath) {
            throw new Exception("Path '{$realpath}' could not be found.");
        }
        
        $this->_targetPath = $realpath;

        return $this;
    }

    public function createSizes(array $sizes)
    {
        $image = $this->_image;
        foreach($sizes as $size) {
            list($width, $height) = $this->_getDimensions($size);
            $this->_createResize($image, intval($width), intval($height));
        }
    }

    protected function _getDimensions($dimensions)
    {
        if (is_array($dimensions)) {
            
            
        }
        else if (is_string($dimensions)) {
            $size = intval($dimensions);
            if(!is_numeric($size)) {
                throw new Exception("Value {$size} is not a valid dimension.");
            }
            $dimensions = array($size, $size);
        }

       return $dimensions;
    }

    /**
     *
     * @param Fizzy_Image $image
     * @param int $width
     * @param int $height
     */
    protected function _createResize(Fizzy_Image $image, $width, $height)
    {
        
        if($image->getWidth() > $image->getHeight()) {
            $image = $image->resizeMaxHeight((int) $height);
        }
        else {
            $image = $image->resizeMaxWidth((int) $width);
        }
        // Crop to the center area of the image
        $image = $image->cropCentered($width, $height);
        
        $filename = $this->_nameTemplate;
        $filename = str_replace('%width%', $width, $filename);
        $filename = str_replace('%height%', $height, $filename);
        $filename = str_replace('%type%', $this->_imageType, $filename);

        $image->save($this->_targetPath . DIRECTORY_SEPARATOR . $filename);
    }

}