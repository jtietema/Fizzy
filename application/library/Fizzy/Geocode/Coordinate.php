<?php

/**
 * Description of Coordinates
 *
 * @author jeroen
 */
class Fizzy_Geocode_Coordinate
{
    protected $_lat = null;
    protected $_lng = null;

    public function getLat()
    {
        return $this->_lat;
    }

    public function setLat($lat)
    {
        $this->_lat = $lat;
        return $this;
    }

    public function getLng()
    {
        return $this->_lng;
    }

    public function setLng($lng)
    {
        $this->_lng = $lng;
        return $this;
    }
}
