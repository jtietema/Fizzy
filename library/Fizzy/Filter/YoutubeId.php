<?php
/**
 * Class Fizzy_Filter_YoutubeId
 * @category Fizzy
 * @package Fizzy_Filter
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

/** Zend_Filter_Interface */
require_once 'Zend/Filter/Interface.php';

/** Zend_Uri_Http */
require_once 'Zend/Uri/Http.php';

/**
 * Filters the id for a YouTube video from a HTTP URI.
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
class Fizzy_Filter_YoutubeId implements Zend_Filter_Interface
{
    /**
     * Filters the YouTube video id from a URL and returns the id. Return an
     * empty string on an invalid URL.
     * 
     * @param string $value
     * @return string
     */
    public function filter($value)
    {
        try {
            $uri = Zend_Uri_Http::factory($value);
            if($uri->valid()) {
                $query = $uri->getQueryAsArray();
                if (isset($query['v'])) {
                    return $query['v'];
                }
            }
        } catch (Zend_Uri_Exception $e) {}
        return '';
    }
}
