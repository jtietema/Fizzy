<?php

/**
 * Class for parsing the configuration.
 *
 * @author jeroen
 */
class Fizzy_Storage_Config
{
    const SQLite = 'SQLite';
    const XML = 'XML';

    protected $_driver = self::SQLite;
    protected $_username = 'test';
    protected $_password = 'test';

    protected $_filename = 'configs/test.db';

    public function __construct()
    {
        // read config file here
        
    }

    public function getDriver()
    {
        return $this->_driver;
    }

    public function getUsername()
    {
        return $this->_username;
    }

    public function getPassword()
    {
        return $this->_password;
    }

    public function getFilename()
    {
        return $this->_filename;
    }
    
}
