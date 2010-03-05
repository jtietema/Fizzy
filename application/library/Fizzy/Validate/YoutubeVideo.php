<?php


/**
 * validator for YouTube video's. Uses the status code of an HTTP request to
 * to the YouTube API to verify a video exists.
 *
 * API URI: http://gdata.youtube.com/feeds/api/videos/VIDEO_ID
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
class Fizzy_Validate_YoutubeVideo extends Zend_Validate_Abstract
{
    const API_URL = 'http://gdata.youtube.com/feeds/api/videos/';
    const INVALID_URI = 'invalidUri';
    const INVALID_VIDEO = 'notAVideo';

    protected $_messageTemplates = array(
        self::INVALID_URI => "'%value%' is not a valid url",
        self::INVALID_VIDEO => "'%value%' is not a valid Youtube video"
    );

    /**
     * Checks if a YouTube video exists
     * @param string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $value = (string) $value;
        $this->_setValue($value);
        
        try {
            $uri = Zend_Uri_Http::factory($value);
        } catch (Zend_Uri_Exception $e) {
            $this->_error(self::INVALID_URI);
            return false;
        }

        $query = $uri->getQueryAsArray();
        if(!isset($query['v'])) {
            $this->_error(self::INVALID_URI);
            return false;
        }
        
        $valid = $this->_checkYoutubeApi($query['v']);
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
        } catch (Zend_Uri_Exception $e) {
            return false;
        }
        
        $httpClient = new Zend_Http_Client($uri);
        $response = $httpClient->request();
        
        return ((boolean) $response->getStatus() == '200');
    }


}
