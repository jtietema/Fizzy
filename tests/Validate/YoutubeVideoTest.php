<?php
require_once 'bootstrap.php';
require_once 'PHPUnit/Framework.php';

require_once 'Fizzy/Validate/YoutubeVideo.php';

class YoutubeVideoTest extends PHPUnit_Framework_TestCase
{
    function testIsValid()
    {
        $video = new Fizzy_Validate_YoutubeVideo();

        $result = $video->isValid('87D17NrgJxU');
        $this->assertTrue($result);

        $result = $video->isValid('87D17NrgJxUUUU');
        $this->assertFalse($result);
    }
}