<?php
/**
 * Class Fizzy_Geocode_Response
 * @package Fizzy_Geocode
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

require_once 'Fizzy/Geocode/Location.php';

/**
 * Adapter indepent response object
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
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
