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
     * @see Fizzy_Interface
     */
    public function persist(Fizzy_Model $model)
    {
        return $this->_driver->persist($model);
    }

    /**
     * @see Fizzy_Interface
     */
    public function remove(Fizzy_Model $model)
    {
        return $this->_driver->remove($model);
    }

    /**
     * @see Fizzy_Interface
     */
    public function fetchOne($type, $uid)
    {
        $array = $this->_driver->fetchOne($type, $uid);

        if ($array === null)
            return null;

        return $this->_buildModel($type, $array);
    }

    /**
     * @see Fizzy_Interface
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
     * @see Fizzy_Interface
     */
    public function fetchColumn($type, $column, $value)
    {
        $array = $this->_driver->fetchColumn($type, $column, $value);

        if ($array === null)
            return null;

        return $this->_buildModel($type, $array);
    }

    /**
     * Instanciates a Model for the given type and populates it with the given
     * array.
     *
     * @param string $type
     * @param array $array
     * @return Fizzy_Model
     */
    protected function _buildModel($type, $array)
    {
        $class = $this->_buildClassname($type);
        $model = new $class();
        $model->populate($array);
        return $model;
    }

    /**
     * Builds a classname for given type
     *
     * @param string $type
     * @return string
     */
    protected function _buildClassname($type)
    {
        return ucfirst($type);
    }
}
