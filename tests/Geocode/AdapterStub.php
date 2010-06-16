<?php
require_once 'Fizzy/Geocode/Adapter/Interface.php';
require_once 'Fizzy/Geocode/Response.php';

class Geocode_AdapterStub  implements Fizzy_Geocode_Adapter_Interface
{
    protected $_query;

    public function getQuery()
    {
        return $this->_query;
    }

    public function setQuery($query)
    {
        $this->_query = $query;
        return $this;
    }
    
    public function location($query = null)
    {
        $response = new Fizzy_Geocode_Response();
        $location = new Fizzy_Geocode_Location(array(
            'address' => 'Marshalllaan 373',
            'zipcode' => '3527 TK',
            'city' => 'Utrecht',
            'country' => 'The Netherlands',
            'lat' => '',
            'lng' => ''
        ));
        $response->addLocation($location);
        return $response;
    }
}