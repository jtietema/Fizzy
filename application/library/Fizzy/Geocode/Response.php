<?php
require_once 'Fizzy/Geocode/Location.php';

/**
 * Description of Response
 *
 * @author jeroen
 */
class Fizzy_Geocode_Response {

    protected $_query = '';
    protected $_errors = null;
    protected $_locations = array();

    /**
     * Returns the first location found
     */
    public function first()
    {
        if (count($this->_locations) > 0){
            return $this->_locations[0];
        }
        throw new Fizzy_Geocode_Exception('No locations set');
    }

    /**
     * Returns all locations found
     */
    public function all()
    {
        return $this->_locations;
    }

    /**
     * The number of locations found
     */
    public function count()
    {
        return count($this->_locations);
    }

    public function isError()
    {
        return (null !== $this->_errors);
    }

    public function getLocations()
    {
        return $this->_locations;
    }

    public function addLocation(Fizzy_Geocode_Location $location)
    {
        $this->_locations[] = $location;
    }

    public function setLocations(array $locations)
    {
        $this->_locations = $locations;
        return $this;
    }

    public function getErrors()
    {
        return $this->_errors;
    }

    public function setErrors(array $errors)
    {
        $this->_errors = $errors;
        return $this;
    }

}
