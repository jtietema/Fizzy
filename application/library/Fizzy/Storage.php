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

/** Fizzy_Storage_Backend_Xml */
require_once 'Fizzy/Storage/Backend/Xml.php';

/** Fizzy_Storage_Backend_Sqlite */
require_once 'Fizzy/Storage/Backend/Sqlite.php';

/** Fizzy_Storage_Backend_Mysql */
require_once 'Fizzy/Storage/Backend/Mysql.php';

/**
 * Main storage Class. You should only use this class to communicate with the
 * persistence layer.
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Storage
{
    /** Constants for storage backend */
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
     * @var Fizzy_Storage_Driver_Interface
     */
    protected $_backend = null;

    /** **/
    
    /**
     * Constructor. Loads the configuration and instanciates the driver.
     * @param array $config
     */
    public function __construct(array $config)
    {
        if(!isset($config['dsn'])) {
            require_once 'Fizzy/Storage/Exception/InvalidConfig.php';
            throw new Fizzy_Storage_Exception_InvalidConfig('No DSN specified in storage configuration.');
        }

        $this->_loadBackend($config);
    }

    /**
     * Loads a backend driver based on the DSN.
     * 
     * @param array $config
     */
    protected function _loadBackend(array $config)
    {
        $dsnParts = explode(':', $config['dsn']);
        $backendString = array_shift($dsnParts);

        switch(strtolower($backendString)) {
            case self::XML:
                $this->_backend = new Fizzy_Storage_Backend_Xml($config);
                break;
            case self::SQLITE:
                $this->_backend = new Fizzy_Storage_Backend_Sqlite($config);
                break;
            case self::MYSQL:
                $this->_backend = new Fizzy_Storage_Backend_Mysql($config);
                break;
            default:
                require_once 'Fizzy/Storage/Exception/InvalidConfig.php';
                throw new Fizzy_Storage_Exception_InvalidConfig("Unsupported backend:" . $backendString);
        }
    }

    /**
     * Sets the storage backend.
     * @param Fizzy_Storage_Backend_Interface $backend
     * @return Fizzy_Storage
     */
    public function setBackend($backend)
    {
        $this->_backend = $backend;
        return $this;
    }

    /**
     * Returns the storage backend.
     * @return Fizzy_Storage_Backend_Interface
     */
    public function getBackend()
    {
        return $this->_backend;
    }

    /**
     * Persists a model in it's container.
     * @param Fizzy_Storage_Model $model
     * @return boolean
     */
    public function persist(Fizzy_Storage_Model $model)
    {
        $container = $this->_getContainerName($model);
        $data = $model->toArray();

        $id = $this->_backend->persist($container, $data, $identifier);

        if(false === $id) {
            return false;
        }

        if(null === $model->getId()) {
            $model->setId($id);
        }

        return true;
    }

    /**
     * Deletes a model from storage.
     * @paran Fizzy_Storage_Model $model
     */
    public function delete(Fizzy_Storage_Model $model)
    {
        $container = $this->_getContainerName($model);
        return $this->_backend->delete($container, $model->getId());
    }

    /**
     * @see Fizzy_Storage_Backend_Interface
     */
    public function fetchOne($class, $identifier)
    {
        $container = $this->_getContainerName($class);
        $data = $this->_backend->fetchOne($container, $identifier);

        if ($data === null) {
            return null;
        }

        return $this->_buildModel($class, $data);
    }

    /**
     * Fetches all items for a model class.
     * @param object|string $class
     * @return array
     */
    public function fetchAll($class)
    {
        $container = $this->_getContainerName($class);
        $data = $this->_backend->fetchAll($container);
        
        $models = array();
        foreach ($data as $identifier => $modelData) {
            $model = $this->_buildModel($class, $modelData);
            $model->setId($identifier);
            $models[$identifier] = $model;
        }
        
        return $models;
    }

    /**
     * Fetches items for a model class by the specified fields.
     * @param object|string $class
     * @param array $fields
     * @return array
     */
    public function fetchByField($class, $fields)
    {
        $container = $this->_getContainerName($class);
        $data = $this->_backend->fetchByColumn($container, $fields);

        $models = array();
        foreach ($data as $identifier => $modelData) {
            $model = $this->_buildModel($class, $modelData);
            $models[$identifier] = $model;
        }

        return $models;
    }

    /**
     * Fetches an item by its identifier.
     * @param string $class
     * @param mixed $identifier
     * @return Fizzy_Storage_Model
     */
    public function fetchByIdentifier($class, $identifier)
    {
        $container = $this->_getContainerName($class);

        $data = $this->_backend->fetchByIdentifier($container, $identifier);

        if(!is_array($data) || 1 !== count($data)) {
            return null;
        }

        $identifier = array_shift(array_keys($data));
        $model = $this->_buildModel($class, array_shift($data));
        $model->setId($identifier);

        return $model;
    }

    /**
     * Alias of {@see fetchByIdentifier}.
     */
    public function fetchByID($class, $identifierValue)
    {
        return $this->fetchByIdentifier($class, $identifierValue);
    }

    /**
     * Instanciates a Model for the given type and populates it with the given
     * array.
     *
     * @param string $class
     * @param array $data
     * @return Fizzy_Storage_Model
     */
    protected function _buildModel($class, array $data)
    {
        $class = $this->_buildClassname($class);
        $model = new $class($data);
        
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

    /**
     * Returns the container name for a model
     * @param object|string $class
     * @return string
     */
    protected function _getContainerName($class)
    {
        // Check if it's a Fizzy_Stroage_Model and has a container name specified
        if($class instanceof Fizzy_Storage_Model) {
            if(is_callable(array($class, 'getContainerName'))) {
                $containerName = $class->getContainerName();
                if(null !== $containerName) {
                    return $containerName;
                }
            } else {
                $class = get_class($class);
            }
        }
        
        // Check if $class is a string, or was converted to a string
        if(is_string($class)) {
            $reflectionClass = new ReflectionClass($class);
            
            if($reflectionClass->hasMethod('getContainerName')) {
                $instance = $reflectionClass->newInstance();
                $containerName = $instance->getContainerName();
                if(null !== $containerName) {
                    return $containerName;
                }
            }
        }

        // No previous solution worked, do it the ugly way
        if(strpos($class, 's') !== strlen($class)) {
            return strtolower($class) . 's';
        } else {
            return strtolower($class);
        }
    }
}
