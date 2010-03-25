<?php
require_once 'Fizzy/Geocode/Coordinate.php';

/**
 * Description of Location
 *
 * @author jeroen
 */
class Fizzy_Geocode_Location
{
    protected $_coordinate = null;

    public function __construct(array $data = null)
    {
        if (null !== $data) {
            if (
                array_key_exists('coordinate', $data) &&
                $data['coordinate'] instanceof Fizzy_Geocode_Coordinate
            ){
                $this->_coordinate = $data['coordinate'];
            }
        }
    }

    public function getCoordinates()
    {
        return $this->_coordinates;
    }

    public function setCoordinates(Fizzy_Geocode_Coordinate $coordinate)
    {
        $this->_coordinate = $coordinate;
        return $this;
    }
}
