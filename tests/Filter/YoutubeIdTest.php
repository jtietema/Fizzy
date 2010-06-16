<?php
require_once 'bootstrap.php';
require_once 'PHPUnit/Framework.php';

require_once 'Fizzy/Filter/YoutubeId.php';

class Filter_YoutubeIdTest extends PHPUnit_Framework_TestCase
{
    function testFilter()
    {
        $filter = new Fizzy_Filter_YoutubeId();

        $result = $filter->filter('http://www.youtube.com/watch?v=M2eiP12hQQY');
        $this->assertEquals('M2eiP12hQQY', $result);

        // invalid youtube link
        $result = $filter->filter('http://www.youtube.com/v/M2eiP12hQQY');
        $this->assertEquals('', $result);

        // not a url
        $result = $filter->filter('Some garbage text');
        $this->assertEquals('', $result);
    }
}
