<?php
/**
 * Interface Fizzy_Image_Adapter_Interface
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
 * Interface for Fizzy_Image adapters.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
interface Fizzy_Image_Adapter_Interface
{

    /**
     * Load image data from a source path.
     * @param <type> $sourcePath
     */
    public function load($sourcePath);

    /**
     * Save the image data to file (destination).
     * @param string $destination
     */
    public function save($destination);

    /**
     * Resize the image
     * @param int $width
     * @param int $height
     */
    public function resize($width, $height);

    /**
     * Crop the image.
     * @param int $startx
     * @param int $starty
     * @param int $width
     * @param int $height
     */
    public function crop($startx, $starty, $width, $height);

    /**
     * Returns the image width.
     * @return int
     */
    public function getImageWidth();

    /**
     * Returns the image  height.
     * @return int
     */
    public function getImageHeight();

}