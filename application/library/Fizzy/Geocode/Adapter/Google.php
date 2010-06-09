<?php
/**
 * Class Fizzy_Geocode_Adapter_Google
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

require_once 'Zend/Http/Client.php';
require_once 'Zend/Json.php';
require_once 'Zend/Uri.php';

require_once 'Fizzy/Geocode/Adapter/Interface.php';
require_once 'Fizzy/Geocode/Response.php';
require_once 'Fizzy/Geocode/Location.php';

/**
 * Adapter voor Google Maps Geocode server V3
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
class Fizzy_Geocode_Adapter_Google implements Fizzy_Geocode_Adapter_Interface
{

    /**
     * Base url for the geocode api
     * @var string
     */
    protected $_apiUrl = 'http://maps.google.com/maps/api/geocode/';

    /**
     * Will be configurable in the future when moer output formats are supported.
     * @var string
     */
    protected $_output = 'json';

    /**
     * The search query to look up the coordinates for
     * @var string
     */
    protected $_query = null;

    /**
     * The country code, specified as a ccTLD ("top-level domain") two-character value.
     * {@see http://code.google.com/apis/maps/documentation/geocoding/index.html#CountryCodes}
     * @var string
     */
    protected $_countryCode = null;

    /**
     * If Google API's should try to use device specific things to determine the
     * location of the device. E.g. a GPS
     * @var boolean
     */
    protected $_sensor = false;

    /**
     * The Http client used. Must be an instance of Zend_Http_Client
     * @var Zend_Http_Client
     */
    protected $_client = null;

    /** **/

    /**
     * Return the Search query
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->_query;
    }

    /**
     * Set the search query
     *
     * @param string $query
     * @return Fizzy_Geocode_Adapter_Google
     */
    public function setQuery($query)
    {
        $this->_query = str_replace(' ', '+', $query);
        return $this;
    }

    /**
     * Set the language code to influence the search results. Language code
     * must be a two value TLD style string.
     *
     * @param string $code
     * @return Fizzy_Geocode_Adapter_Google
     */
    public function setCountryCode($code)
    {
        $this->_countryCode = strtoupper($code);
        return $this;
    }

    /**
     * Returns the country code that will be used in the search query.
     *
     * @return null|string
     */
    public function getCountryCode()
    {
        return $this->_countryCode;
    }

    public function getSensor()
    {
        return $this->_sensor;
    }

    /**
     * @param boolean $sensor
     */
    public function setSensor($sensor)
    {
        $this->_sensor = (boolean) $sensor;
        return $this;
    }

    /**
     * Returns the current client instance if any.
     * @return Zend_Http_Client|null
     */
    public function getHttpClient()
    {
        return $this->_client;
    }

    /**
     * Set the Http client to use for making the requests. This is usefull if
     * you what Zend_Http_Client to use a specific Zend_Http_Client adapter.
     * @param Zend_Http_Client $client
     * @return Fizzy_Geocode_Adapter_Google
     */
    public function setHttpClient(Zend_Http_Client $client)
    {
        $this->_client = $client;
        return $this;
    }

    public function location($query = null)
    {
        if(null !== $query) {
            $this->setQuery($query);
        }

        // Build the request parameters
        $parameters = array (
            'address' => $this->_query,
            'sensor' => ($this->_sensor ? 'true' : 'false')
        );
        if(null !== $this->_countryCode) {
            $parameters['lg'] = $this->_countryCode;
        }

        // Build the request URI
        $uri = Zend_Uri::factory($this->_apiUrl . $this->_output);
        $uri->setQuery($parameters);

        // Send the request
        if  ($this->_client === null){
            $this->_client = new Zend_Http_Client($uri);
        } else {
            $this->_client->setUri($uri);
        }
        $httpResponse = $this->_client->request();
        $json = $httpResponse->getBody();
        
        // parse the response into a Fizzy_Geocode_Response
        $response = new Fizzy_Geocode_Response();
        $responseArray = Zend_Json::decode($json);
        
        if ($responseArray['status'] != 'OK'){
            $response->setErrors(array($responseArray['status']));
        }

        foreach ($responseArray['results'] as $result){
            $location = new Fizzy_Geocode_Location();
            $location->setLat($result['geometry']['location']['lat']);
            $location->setLng($result['geometry']['location']['lng']);

            $address = array(
                'number' => null,
                'street' => null,
                'city' => null,
                'zipcode' => null,
                'country' => null
            );
            foreach ($result['address_components'] as $component){
                if (in_array('street_number', $component['types'])){
                    $address['number'] = $component['long_name'];
                }
                if (in_array('route', $component['types'])){
                    $address['street'] = $component['long_name'];
                }
                if (in_array('locality', $component['types'])){
                    $address['city'] = $component['long_name'];
                }
                if (in_array('country', $component['types'])){
                    $address['country'] = $component['long_name'];
                }
                if (in_array('postal_code', $component['types'])){
                    $address['zipcode'] = $component['long_name'];
                }
            }

            $location->setAddress($address['street'] . ' ' . $address['number']);
            $location->setZipcode($address['zipcode']);
            $location->setCity($address['city']);
            $location->setCountry($address['country']);
            
            $response->addLocation($location);
        }

        return $response;
    }
}
