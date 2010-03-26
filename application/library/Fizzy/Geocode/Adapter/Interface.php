<?php
interface Fizzy_Geocode_Adapter_Interface
{
    public function getQuery();
    public function setQuery($query);
    public function location($query);
    
}