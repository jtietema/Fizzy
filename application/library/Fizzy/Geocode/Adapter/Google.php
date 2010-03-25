<?php
require_once 'Zend/Http/Client.php';
require_once 'Zend/Json.php';
require_once 'Zend/Uri.php';

/**
 * Description of Google
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
class Fizzy_Geocode_Adapter_Google implements Fizzy_Geocode_Adapter_Interface
{

    /**
     * Base url for the geocode api
     * @var string
     */
    protected $_apiUrl = 'http://maps.google.com/maps/geo';

    /**
     * Will be configurable in the future when moer output formats are supported.
     * @var string
     */
    protected $_output = 'json';

    /**
     * The Google maps API key
     * @var string
     */
    protected $_apiKey = null;

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
     * Geocode constructor. Accepts the geocode API key as parameter.
     * @param string $apiKey
     */
    public function __construct($apiKey = null)
    {
        if (null !== $apiKey) {
            $this->setApiKey($apiKey);
        }
    }

    /**
     * Returns the API key.
     * @return string
     */
    public function getApiKey()
    {
        return $this->_apiKey;
    }

    /**
     * Set the Google Maps API key
     *
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->_apiKey = $apiKey;
        return $this;
    }

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

    public function location($query)
    {
        if(null !== $query) {
            $this->setQuery($query);
        }

        // Build the request parameters
        $parameters = array (
            'q' => $this->_query,
            'key' => $this->_apiKey,
            'output' => $this->_output,
            'sensor' => ($this->_sensor ? 'true' : 'false')
        );
        if(null !== $this->_countryCode) {
            $parameters['lg'] = $this->_countryCode;
        }

        // Build the request URI
        $uri = Zend_Uri::factory($this->_apiUrl);
        $uri->setQuery($parameters);

        // Send the request
        if  ($this->_client === null)
            $this->_client = new Zend_Http_Client($uri);
        $response = $this->_client->request();

        /*
         * @todo parse the response into a Fizzy_Geocode_Response object
         */
        // Return only the response body
        $json = $response->getBody();

        return Zend_Json::decode($json);
    }
}
