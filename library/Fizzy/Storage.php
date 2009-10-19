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

    const SQLite = 'sqlite';
    const XML = 'xml';
    
    /**
     * The config object.
     * @var Fizzy_Storage_Config
     */
    protected $_config = null;

    /**
     * The persistence driver used.
     * @var Fizzy_Storage_Interface
     */
    protected $_driver = null;

    /**
     * The constructor.
     * Loads the config and instanciates the driver.
     */
    public function __construct($dsn)
    {
        if ($this->_getDriver($dsn) === self::SQLite)
        {
            $this->_driver = new Fizzy_Storage_SQLite($dsn);
        }
        elseif ($this->_getDriver($dsn) === self::XML)
        {
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
       
       if (strtolower($pieces[0]) === self::SQLite)
       {
           return self::SQLite;
       }
       elseif (strtolower($pieces[0]) === self::XML)
       {
           return self::XML;
       }
       else
       {
           require_once 'Fizzy/Storage/Exception/InvalidConfig.php';
           throw new Fizzy_Storage_Exception_InvalidConfig("Unsupported driver:" . $pieces[0]);
       }
    }

    /**
     * Persist the given model (add or save).
     * Returns the persisted model (with added id).
     * 
     * @param Fizzy_Model $model
     * @return Fizzy_Model
     */
    public function persist(Fizzy_Model $model)
    {
        return $this->_driver->persist($model);
    }

    /**
     * Remove the given model from persistence.
     *
     * @param Fizzy_Model $model
     */
    public function remove(Fizzy_Model $model)
    {
        return $this->_driver->remove($model);
    }

    /**
     * Fetch one item of $type with $uid.
     * 
     * @param string $type
     * @param mixed $uid
     * @return Fizzy_Model|null
     */
    public function fetchOne($type, $uid)
    {
        $array = $this->_driver->fetchOne($type, $uid);

        if ($array === null)
            return null;

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

        foreach ($results as $array)
        {
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
     * @return Fizzy_Model|null
     */
    public function fetchColumn($type, $column, $value)
    {
        $array = $this->_driver->fetchColumn($type, $column, $value);

        if ($array === null)
            return null;

        return $this->_buildModel($type, $array);
    }

    protected function _buildModel($type, $array)
    {
        $class = $this->_buildClassname($type);
        $model = new $class();
        $model->populate($array);
        return $model;
    }

    protected function _buildClassname($type)
    {
        return ucfirst($type);
    }
}
