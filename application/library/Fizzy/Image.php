<?php
/**
 * Class Fizzy_Image
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
 * This represents an image that can be manipulated. It uses a backend to
 * perform the manipulations and save them to a file.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Image
{
    /**
     * The adapter to use for image transformations.
     * @var Fizzy_Image_Adapter_Abstract
     */
    protected $_adapter = null;

    /** **/

    /**
     * Constructor
     * @param Fizzy_Image_Adapter_Interface|string $adapter
     * @param string $source
     */
    public function __construct($adapter, $source = null)
    {
        $this->setAdapter($adapter);
        
        if(null !== $source) {
            $this->load($source);
        }
    }

    /**
     * Sets the adapter for image manipulation.
     * @param Fizzy_Image_Adapter_Interface|string $adapter
     * @return Fizzy_Image
     */
    public function setAdapter($adapter)
    {
        if($adapter instanceof Fizzy_Image_Adapter_Interface) {
            $this->_adapter = $adapter;
        }
        else if(is_string($adapter)) {
            $adapter = trim($adapter);
            // Just create the class, autoloading will take care of the existence
            $this->_adapter = new $adapter;
        }
        else {
            throw new Fizzy_Image_Exception('Adapter must be a string or an instance of Fizzy_Image_Adapter_Interface.');
        }

        return $this;
    }

    /**
     * Returns the adapeter for image manipulation. Checks the if an adapter is
     * set and if it implements Fizzy_Image_Adapter_Interface.
     * @throws Fizzy_Image_Exception
     * @return Fizzy_Image_Adapter_Interface
     */
    public function getAdapter()
    {
        if(null === $this->_adapter) {
            throw new Fizzy_Image_Exception('No adapter was set.');
        }
        if(!($this->_adapter instanceof Fizzy_Image_Adapter_Interface)) {
            throw new Fizzy_Image_Exception('Adapter ' . get_class($this->_adapter) . ' does not implement Fizzy_Image_Adapter_Interface.');
        }

        return $this->_adapter;
    }

    /**
     * Returns the width of the image.
     * @return int
     */
    public function getWidth()
    {
        return $this->_adapter->getImageWidth();
    }

    /**
     * Returns the height of the image.
     * @return int
     */
    public function getHeight()
    {
        return $this->_adapter->getImageHeight();
    }

    /**
     * Returns a copy of the image object.
     * @return Fizzy_Image
     */
    public function getCopy()
    {
        $adapter = $this->_getAdapterClone();
        return new self($adapter);
    }

    /**
     * Load an image source.
     * @param string $source
     * @return Fizzy_Image
     * @todo Add checks to see if the image file exists.
     */
    public function load($source)
    {
        $this->getAdapter()->load($source);
        
        return $this;
    }

    /**
     * Save the image to a destination.
     * @param string $destination
     */
    public function save($destination)
    {
        if(empty($destination)) {
            throw new Fizzy_Image_Exception('Save path cannot be empty.');
        }
        
        $this->getAdapter()->save($destination);
        return $this;
    }

    /**
     * Outputs the image data to the browser.
     * @return mixed
     */
    public function output()
    {
        throw new Fizzy_Image_Exception('Not implemented');
    }

    /**
     * Resize the image
     * @param int $width
     * @param int $height
     * @return Fizzy_Image
     */
    public function resize($width, $height)
    {
        $adapter = $this->_getAdapterClone();
        $adapter->resize($width, $height);
        $image = new self($adapter);

        return $image;
    }

    /**
     * Resize the image to a maximum width maintaining the heights aspect ratio.
     * @param int $width
     * @return Fizzy_Image
     */
    public function resizeMaxWidth($width)
    {
        $heightRatio = $this->getHeight() / $this->getWidth();
        $height = round($width * $heightRatio);
        return $this->resize((int) $width, (int) $height);
    }

    /**
     * Resize the image to a maximum height maintaining the widths aspect ratio.
     * @param int $height
     * @return Fizzy_Image
     */
    public function resizeMaxHeight($height)
    {
        $widthRatio = $this->getWidth() / $this->getHeight();
        $width = round($height * $widthRatio);
        return $this->resize((int) $width, (int) $height);
    }

    /**
     * Crop the image
     * @param int $startx
     * @param int $starty
     * @param int $width
     * @param int $height
     * @return Fizzy_Image
     */
    public function crop($startx, $starty, $width, $height)
    {
        $adapter = $this->_getAdapterClone();
        $adapter->crop($startx, $starty, $width, $height);
        $image = new self($adapter);

        return $image;
    }

    /**
     * Crops the image to an area around the center of the image.
     * @param int $width
     * @param int $height
     */
    public function cropCentered($width, $height)
    {
        $centerX = round($this->getWidth() / 2);
        $centerY = round($this->getHeight() / 2);

        $startX = $centerX - round($width / 2);
        $startY = $centerY - round($height / 2);

        return $this->crop($startX, $startY, $width, $height);
    }

    /**
     * Returns a clone of the adapter.
     * @return Fizzy_Image_Adapter_Interface
     */
    protected function _getAdapterClone()
    {
        return clone $this->_adapter;
    }

    /**
     * Scales the image to precentage of the original.
     * @param string $percent
     * @return Fizzy_Image
     */
    public function scale($percent)
    {
        throw new Fizzy_Image_Exception('Not implemented');
    }

    /**
     * Rotates the image given degree in given direction.
     * Direction can be:
     * <ul>
     *  <li>right</li>
     *  <li>left</li>
     *  <li>cw (clockwise)</li>
     *  <li>ccw (counter clockwise)</li>
     * </ul>
     * @param int $degree
     * @param string $direction
     * @return Fizzy_Image
     */
    public function rotate($degree, $direction)
    {
        throw new Fizzy_Image_Exception('Not implemented');
    }

    /**
     * Flips the image around the horizontal axis.
     * @return Fizzy_Image
     */
    public function flipHorizontal()
    {
        throw new Fizzy_Image_Exception('Not implemented');
    }

    /**
     * Flips the image around the vertical axis.
     * @return Fizzy_Image
     */
    public function flipVertical()
    {
        throw new Fizzy_Image_Exception('Not implemented');
    }

}
