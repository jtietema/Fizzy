<?php
/**
 * Class Fizzy_Geocode_Location
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

/**
 * An adapter indepent representation of a location
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
class Fizzy_Geocode_Location
{
    /**
     * Array with keys `lat` and `lng`
     * @var array|null
     */
    protected $_lat = null;
    protected $_lng = null;
    
    protected $_address = null;
    protected $_zipcode = null;
    protected $_city = null;
    protected $_country = null;

    public function __construct(array $data = null)
    {
        if (isset($data['address'])){
            $this->setAddress($data['address']);
        }
        if (isset($data['zipcode'])){
            $this->setZipcode($data['zipcode']);
        }
        if (isset($data['city'])){
            $this->setCity($data['city']);
        }
        if (isset($data['country'])){
            $this->setCountry($data['country']);
        }
        if (isset($data['lat'])){
            $this->setLat($data['lat']);
        }
        if (isset($data['lng'])){
            $this->setLng($data['lng']);
        }
    }

    public function getCoordinates()
    {
        return $this->_coordinates;
    }

    public function setCoordinates($lat, $lng)
    {
        $this->_lat = $lat;
        $this->_lng = $lng;
        return $this;
    }

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

    public function getAddress()
    {
        return $this->_address;
    }

    public function setAddress($address)
    {
        $this->_address = $address;
        return $this;
    }

    public function getZipcode()
    {
        return $this->_zipcode;
    }

    public function setZipcode($zipcode)
    {
        $this->_zipcode = $zipcode;
        return $this;
    }

    public function getCity()
    {
        return $this->_city;
    }

    public function setCity($city)
    {
        $this->_city = $city;
        return $city;
    }

    public function getCountry()
    {
        return $this->_country;
    }

    public function setCountry($country)
    {
        $this->_country = $country;
        return $this;
    }
}
