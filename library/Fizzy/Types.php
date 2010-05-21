<?php
/**
 * Class Fizzy_Type
 * @package Fizzy
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
 * Convenience class for managing all type information from type config file.
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
class Fizzy_Types {

    protected static $_instance = null;

    protected $_config;

    protected function __construct(Zend_Config $config)
    {
        $this->_config = $config;
    }

    public static function initialize(Zend_Config $config)
    {
        if (null !== self::$_instance){
            throw new Fizzy_Exception('Can\'t call Fizzy_Type::initialize twice.');
        }
        self::$_instance = new self($config);
        return self::$_instance;
    }

    /**
     * Returns the current instance, but only if it has been initialized in the
     * bootstrap.
     * 
     * @return Fizzy_Type
     */
    public static function getInstance()
    {
        if (null === self::$_instance){
            throw new Fizzy_Exception('Type configuration not initialized. Make sure you call "Fizzy_Type::initialize($config)" first');
        }
        return self::$_instance;
    }

    public function hasType($type)
    {
        return isset($this->_config->Types->{$type});
    }

    public function getType($type)
    {
        if (isset($this->_config->Types->{$type})){
            return $this->_config->Types->{$type};
        }
        return null;
    }

    public function getTypeModel($type)
    {
        if (isset($this->_config->Types->{$type}->model)){
            return $this->_config->Types->{$type}->model;
        }
        return null;
    }
}
