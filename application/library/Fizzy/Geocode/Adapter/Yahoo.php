<?php
require_once 'Zend/Uri.php';
require_once 'Zend/Http/Client.php';

require_once 'Fizzy/Geocode/Adapter/Interface.php';
require_once 'Fizzy/Geocode/Exception.php';
require_once 'Fizzy/Geocode/Response.php';
require_once 'Fizzy/Geocode/Location.php';

/**
 * Description of Yahoo
 *
 * @author jeroen
 */
class Fizzy_Geocode_Adapter_Yahoo implements Fizzy_Geocode_Adapter_Interface
{
    protected $_apiKey = null;

    protected $_output = 'php';

    protected $_apiUrl = 'http://local.yahooapis.com/MapsService/V1/geocode';

    protected $_query = null;

    protected $_httpClient = null;

    public function __construct($apiKey = null)
    {
        if (null !== $apiKey){
            $this->_apiKey = $apiKey;
        }
    }

    public function getApiKey()
    {
        return $this->_apiKey;
    }

    public function setApiKey($apiKey)
    {
        $this->_apiKey = $apiKey;
        return $this;
    }

    public function getQuery()
    {
        return $this->_query;
    }

    public function setQuery($query)
    {
        $this->_query = $query;
        return $this;
    }

    public function getHttpClient()
    {
        return $this->_httpClient;
    }

    public function setHttpClient(Zend_Http_Client $client)
    {
        $this->_httpClient = $client;
        return $this;
    }

    public function location($query = null)
    {
        if (null !== $query){
            $this->setQuery($query);
        }

        if (null === $this->_query){
            throw new Fizzy_Geocode_Exception('No query passed');
        }

        $parameters = array(
            'appid' => $this->_apiKey,
            'location' => $this->_query,
            'output' => $this->_output
        );

        $uri = Zend_Uri::factory($this->_apiUrl);
        $uri->setQuery($parameters);

        if (null === $this->_httpClient){
            $this->_httpClient = new Zend_Http_Client($uri);
        } else {
            $this->_httpClient->setUri($uri);
        }

        $httpResponse = $this->_httpClient->request();

        $response = new Fizzy_Geocode_Response();

        if (200 != $httpResponse->getStatus()){
            if (400 == $httpResponse->getStatus()){
                $response->setErrors(array('Bad request'));
            } elseif (403 == $httpResponse->getStatus()){
                $response->setErrors(array('Forbidden'));
            } elseif (503 == $httpResponse->getStatus()){
                $response->setErrors(array('Service unavailable'));
            }
            return $response;
        }

        $php = unserialize($httpResponse->getBody());
        var_dump($php);

        foreach ($php['ResultSet'] as $result){
            $location = new Fizzy_Geocode_Location();
            $location->setAddress($result['Address']);
            $location->setZipcode($result['Zip']);
            $location->setCity($result['City']);
            $location->setCountry($result['Country']);

            $location->setLat($result['Latitude']);
            $location->setLng($result['Longitude']);
            
            $response->addLocation($location);
        }
        return $response;
    }
}
