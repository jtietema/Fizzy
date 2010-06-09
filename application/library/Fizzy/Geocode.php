<?php

/**
 * This class provides a uniform API for making Geocode requests to different
 * Geocode providers. The Class is adapter based.
 * Currently it supports:
 * - Google
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
class Fizzy_Geocode {
    protected $_adapter = null;

    public function __construct(Fizzy_Geocode_Adapter_Interface $adapter = null)
    {
        if (null !== $adapter) {
            $this->_adapter = $adapter;
        }
    }

    public function getAdapter()
    {
        return $this->_adapter;
    }

    public function setAdapter(Fizzy_Geocode_Adapter_Interface $adapter)
    {
        $this->_adapter = $adapter;
        return $this;
    }

    /**
     * Fires a Geocode query
     * @param <type> $query
     */
    public function location($query)
    {
        return $this->_adapter->location($query);
    }

}
