<?php
/**
 * Class Fizzy_Validate_YoutubeVideo
 * @category Fizzy
 * @package Fizzy_Validate
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

/** Zend_Validate_Abstract */
require_once 'Zend/Validate/Abstract.php';

/** Zend_Uri_Http */
require_once 'Zend/Uri/Http.php';

/** Zend_Http_Client */
require_once 'Zend/Http/Client.php';

/**
 * Validator for YouTube video's. Uses the status code of an HTTP request to
 * to the YouTube API to verify a video exists.
 *
 * API URI: http://gdata.youtube.com/feeds/api/videos/VIDEO_ID
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
class Fizzy_Validate_YoutubeVideo extends Zend_Validate_Abstract
{

    const API_URL = 'http://gdata.youtube.com/feeds/api/videos/';
    const INVALID_VIDEO = 'notAVideo';

    /**
     * @see Zend_Validate_Abstract
     */
    protected $_messageTemplates = array(
        self::INVALID_VIDEO => "'%value%' is not a valid Youtube video"
    );

    /**
     * Checks if a YouTube video exists with the given ID
     * 
     * @param string $value - The Youtube video ID
     * @return boolean
     */
    public function isValid($value)
    {
        $value = (string) $value;
        $this->_setValue($value);
        
        $valid = $this->_checkYoutubeApi($value);
        if(false === $valid) {
            $this->_error(self::INVALID_VIDEO);
            return false;
        }

        return true;
    }

    /**
     * Does a request to the YouTube API.
     * @param string|int $id
     * @return boolean
     */
    protected function _checkYoutubeApi($id)
    {
        try {
            $uri = Zend_Uri_Http::factory(self::API_URL . $id);

            $httpClient = new Zend_Http_Client($uri);
            $response = $httpClient->request();
        } catch (Zend_Uri_Exception $e) {
            // the id is malformed
            return false;
        } catch (Zend_Http_Client_Adapter_Exception $e){
            /**
             * Catch the timeout
             *
             * We do not know if the video is valid and are unable to check
             * so we assume it is OK.
             */
            return true;
        }

        return $response->getStatus() == '200';
    }


}
