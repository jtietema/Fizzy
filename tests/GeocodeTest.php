<?php
require_once 'bootstrap.php';
require_once 'PHPUnit/Framework.php';
require_once 'Geocode/AdapterStub.php';

require_once 'Fizzy/Geocode.php';

class GeocodeTest extends PHPUnit_Framework_TestCase
{
    function testConstruct()
    {
        $geocode = new Fizzy_Geocode();
        $this->assertNull($geocode->getAdapter());

        $adapter = new Geocode_AdapterStub();
        $geocode = new Fizzy_Geocode($adapter);
        $this->assertEquals($adapter, $geocode->getAdapter());
        
    }

    function testSetAdapter()
    {
        $geocode = new Fizzy_Geocode();
        $this->assertNull($geocode->getAdapter());
        $adapter = new Geocode_AdapterStub();
        $this->assertEquals($geocode, $geocode->setAdapter($adapter));
        $this->assertEquals($adapter, $geocode->getAdapter());
    }

    function testLocation()
    {
        $geocode = new Fizzy_Geocode(new Geocode_AdapterStub());
        $response = $geocode->location('Something');
        $this->assertTrue($response instanceof Fizzy_Geocode_Response);
    }
}