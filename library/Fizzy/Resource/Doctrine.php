<?php
/**
 * Class Fizzy_Resource_Doctrine
 * @category Fizzy
 * @package Fizzy_Resource
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
 * @copyright Copyright (c) 2009-2010 Voidwalkers (http://www.voidwalkers.nl)
 * @license http://www.voidwalkers.nl/license/new-bsd The New BSD License
 */

/**
 * Resource for bootstrapping Doctrine
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 * @todo enable binding models to a connection
 * @todo Add support for connection options (as array)
 */
class Fizzy_Resource_Doctrine extends Zend_Application_Resource_ResourceAbstract
{
    
    /**
     * Doctrine_Manager instance
     * @var Doctrine_Manager
     */
    protected $_manager = null;
    
    /** **/

    /**
     * Initializes the Doctrine Resource. This is done in four steps:
     * <ul>
     *  <li>Register Doctrines own autoloader with the Zend_Loader stack</li>
     *  <li>Instantiate the Doctrine_Manager</li>
     *  <li>Open defined connections</li>
     *  <li>Load model paths</li>
     * </ul>
     * @return Doctrine_Manager
     */
    public function init()
    {
        // Get the Resource options
        $options = $this->getOptions();

        // Register the Doctrine autoloader
        require_once 'Doctrine.php';
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->pushAutoloader(array('Doctrine', 'autoload'));
        $autoloader->pushAutoloader(array('Doctrine_Core', 'modelsAutoload'));

        // Initialize the Doctrine Manager
        $this->_manager = Doctrine_Manager::getInstance();
        // Check for manager attributes in the options
        if(array_key_exists('manager', $options)) {
            $managerOptions = $options['manager'];
            if(array_key_exists('attributes', $managerOptions) && is_array($managerOptions['attributes'])) {
                $this->_setManagerAttributes($managerOptions['attributes']);
            }
        }

        // Open defined connections
        if(isset($options['connections']) && is_array($options['connections']) && !empty($options['connections']))         {
            foreach($options['connections'] as $connectionName => $connectionOptions) {
                $this->_openConnection($connectionName, $connectionOptions);
            }
        }

        // Load models
        if(array_key_exists('paths', $options)) {
            $pathsOptions = $options['paths'];
            if(array_key_exists('models_path', $pathsOptions) && !empty($pathsOptions['models_path'])) {
                $this->_loadModels($pathsOptions['models_path']);
            }
        }
        
        return $this->_manager;
    }

    /**
     * Sets attributes on the Doctrine_Manager. Doctrine constants are supported
     * and can be supplied in lower or upper case. Doctrine constants and arrays
     * as values are also supported.
     * @param array $attributes
     */
    protected function _setManagerAttributes(array $attributes)
    {
        $reflect = new ReflectionClass('Doctrine');
        $doctrineConstants = $reflect->getConstants();

        $manager = Doctrine_Manager::getInstance();

        foreach($attributes as $attribute => $value)
        {
            if (!array_key_exists(strtoupper($attribute), $doctrineConstants)) {
                require_once 'Zend/Application/Resource/Exception.php';
                throw new Zend_Application_Resource_Exception('Invalid Doctrine_Manager attribute.');
            }

            // Get the value for the attribute
            $attributeKey = $doctrineConstants[strtoupper($attribute)];

            // Check if the attribute value is a doctrine constant as well
            if(array_key_exists(strtoupper($value), $doctrineConstants)) {
                $attributeValue = $doctrineConstants[strtoupper($value)];
            }
            // Check if the value is an array itself
            else if(is_array($value)) {
                $attributeValue = array();
                foreach($value as $subKey => $subValue) {
                    $attributeValue[$subKey] = $subValue;
                }
            }
            else {
                $attributeValue = $value;
            }

            $manager->setAttribute($attributeKey, $attributeValue);
        }
    }


    /**
     * Opens a connection with the provided options.
     * @param string $name
     * @param array $options
     */
    protected function _openConnection($name, array $options)
    {
        $dsn = (is_string($options['dsn'])) ? $options['dsn'] : $this->_constructDsn($options['dsn']);

        // Initialize a connection
        Doctrine_Manager::connection($dsn, $name);
    }

    /**
     * Loads model classes for Doctrine. The path can be supplied as a string or
     * as an array.
     * @param string|array $modelsPath
     */
    protected function _loadModels($modelsPath)
    {
        if(is_string($modelsPath)) {
            Doctrine::loadModels($modelsPath);
        }
        else if(is_array($modelsPath)) {
            foreach($modelsPath as $path) {
                Doctrine::loadModels($path);
            }
        }
        else {
            require_once 'Zend/Application/Resource/Exception.php';
            throw new Zend_Application_Resource_Exception('Invalid Doctrine model_path option.');
        }
    }


    /**
     * Construct a DSN from an array.
     * Supported keywords:
     * <ul>
     *  <li>phptype</li>
     *  <li>dbsyntax</li>
     *  <li>protocol</li>
     *  <li>hostspec</li>
     *  <li>database</li>
     *  <li>username</li>
     *  <li>password</li>
     *  <li>option</li>
     * </ul>
     * @param array $options
     * @link http://www.doctrine-project.org/documentation/manual/1_1/en/introduction-to-connections#dsn,-the-data-source-name
     */
    protected function _constructDsn($options)
    {
        $dsn = '';

        if(!isset($options['phptype'])) {
            require_once 'Zend/Application/Resource/Exception.php';
            throw new Zend_Application_Resource_Exception('The phptype options is manditory for connection specification.');
        }
        else if(!isset($option['username'])) {
            require_once 'Zend/Application/Resource/Exception.php';
            throw new Zend_Application_Resource_Exception('The username options is manditory for connection specification.');
        }
        else if(!isset($options['hostspec'])) {
            require_once 'Zend/Application/Resource/Exception.php';
            throw new Zend_Application_Resource_Exception('The hostspec options is manditory for connection specification.');
        }
        else if(!isset($options['database'])) {
            require_once 'Zend/Application/Resource/Exception.php';
            throw new Zend_Application_Resource_Exception('The database options is manditory for connection specification.');
        }

        $dsn .= $options['phptype'] . '://' . $options['username'];
        if(isset($options['password'])) {
            $dsn .= ':' . $options['password'];
        }
        $dsn .= '@';
        if(isset($options['protocol'])) {
            $dsn .= $options['protocol'] . '+';
        }
        $dsn .= $options['hostspec'] . '/' . $options['database'];

        return $dsn;
    }

}