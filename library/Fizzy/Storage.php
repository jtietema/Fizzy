<?php
/**
 * Class Fizzy_Storage
 * @package Fizzy
 * @subpackage Storage
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
 * @copyright Copyright (c) 2009 Voidwalkers (http://www.voidwalkers.nl)
 * @license http://www.voidwalkers.nl/license/new-bsd The New BSD License
 */

 /** Fizzy_Storage_SQLite */
require_once 'Fizzy/Storage/SQLite.php';

/** Fizzy_Storage_XML */
require_once 'Fizzy/Storage/XML.php';

/**
 * Main storage Class. You should only use this class to communicate with the
 * persistence layer.
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
class Fizzy_Storage
{
    /** Constants for drivers */
    const MYSQL = 'mysql';
    const SQLITE = 'sqlite';
    const XML = 'xml';
    
    /**
     * The storage configuration.
     * @var array
     */
    protected $_config = null;

    /**
     * The persistence driver used.
     * @var Fizzy_Storage_Interface
     */
    protected $_driver = null;

    /** **/
    
    /**
     * Constructor. Loads the configuration and instanciates the driver.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->_config = $config;

        if(!isset($this->_config['dsn'])) {
            require_once 'Fizzy/Storage/Exception/InvalidConfig.php';
            throw new Fizzy_Storage_Exception_InvalidConfig('No DSN specified in storage configuration.');
        }

        $dsn = strtolower($this->_config['dsn']);
        if ($this->_getDriver($dsn) === self::SQLITE) {
            $this->_driver = new Fizzy_Storage_SQLite($dsn);
        }
        elseif ($this->_getDriver($dsn) === self::XML) {
            $this->_driver = new Fizzy_Storage_XML($dsn);
        } 
    }

    /**
     * Parses the dsn and returns the driver to be used.
     * 
     * @param string $dsn
     * @return string
     */
    protected function _getDriver($dsn)
    {
       $pieces = explode(':', $dsn);
       
       if (strtolower($pieces[0]) === self::SQLITE) {
           return self::SQLITE;
       }
       elseif (strtolower($pieces[0]) === self::XML) {
           return self::XML;
       }
       else {
           require_once 'Fizzy/Storage/Exception/InvalidConfig.php';
           throw new Fizzy_Storage_Exception_InvalidConfig("Unsupported driver:" . $pieces[0]);
       }
    }

    /**
     * Persist the given model (add or save).
     * Returns the persisted model (with added id).
     * 
     * @param Fizzy_Storage_Model $model
     * @return Fizzy_Storage_Model
     */
    public function persist(Fizzy_Storage_Model $model)
    {
        return $this->_driver->persist($model);
    }

    /**
     * Remove the given model from persistence.
     *
     * @param Fizzy_Storage_Model $model
     */
    public function remove(Fizzy_Storage_Model $model)
    {
        return $this->_driver->remove($model);
    }

    /**
     * Fetch one item of $type with $uid.
     * 
     * @param string $type
     * @param mixed $uid
     * @return Fizzy_Storage_Model|null
     */
    public function fetchOne($type, $uid)
    {
        $array = $this->_driver->fetchOne($type, $uid);

        if ($array === null) {
            return null;
        }

        return $this->_buildModel($type, $array);
    }

    /**
     * Fetch all entities from a specific type (e.g. pages, users).
     * 
     * @param string $type
     * @return array
     */
    public function fetchAll($type)
    {
        $results = $this->_driver->fetchAll($type);
        
        $models = array();
        foreach ($results as $array) {
            $models[] = $this->_buildModel($type, $array);
        }

        return $models;
    }

    /**
     * Used to select a row by a Value in a specific column
     * 
     * @param string $type
     * @param string $column
     * @param mixed $value
     * @return Fizzy_Storage_Model|null
     */
    public function fetchColumn($type, $column, $value)
    {
        $array = $this->_driver->fetchColumn($type, $column, $value);

        if ($array === null) {
            return null;
        }

        return $this->_buildModel($type, $array);
    }

    /**
     * Builds a model for the given type and fills it with the data array.
     * @param string $type
     * @param array $data
     * @return Fizzy_Storage_Model
     */
    protected function _buildModel($type, $data)
    {
        $class = $this->_buildClassname($type);
        $model = new $class($data);
        
        return $model;
    }

    protected function _buildClassname($type)
    {
        return ucfirst($type);
    }
}
