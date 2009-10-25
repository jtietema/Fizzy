<?php
/**
 * Abstract Class Fizzy_Model
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
 * @copyright Copyright (c) 2009 Voidwalkers (http://www.voidwalkers.nl)
 * @license http://www.voidwalkers.nl/license/new-bsd The New BSD License
 */
 
/**
 * Model base class with convenience method to handle getting and setting of
 * data.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
abstract class Fizzy_Model implements IteratorAggregate, Countable
{

    /**
     * Data container for internal storage of data.
     * @var array
     */
    private $_data = array();

    /** **/

    /**
     * Constructor. Accepts an array with data which populates the data
     * container.
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        foreach($data as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Overload to prevent protected or private variables from being loaded.
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if(0 === strpos($name, '_')) {
            require_once 'Fizzy/Exception.php';
            throw new Fizzy_Exception("Cannot access private or protected variables.");
        }
        
        if(isset($this->_data[$name])) {
            return $this->_data[$name];
        }

        trigger_error("Variable {$name} not found.");
        return null;
    }

    /**
     * Overload to prevent private or protected variables from being set.
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        if(0 === strpos($name, '_')) {
            require_once 'Fizzy/Exception.php';
            throw new Fizzy_Exception("Cannot set private or protected variables.");
        }

        $this->_data[$name] = $value;
    }

    /**
     * Overload to support camel cased getters and setters for variables. Camel
     * cased variables names are transformed by splitting them on capitals and
     * adding underscores between the lower cased parts.
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if(0 === strpos($name, 'get')) {
            return $this->__get($this->_methodToVar($name, 'get'));
        }
        else if(0 === strpos($name, 'set')) {
            return $this->__set($this->_methodToVar($name, 'set'), array_shift($arguments));
        }
    }

    /**
     * Override to check the internal data container.
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    /**
     * Override to calculate for the internal data container.
     * @param string $name
     */
    public function __unset($name)
    {
        if(isset($this->_data[$name])) {
            unset($this->_data[$name]);
        }
    }

    /**
     * Returns the model data as an array.
     * @return array
     */
    public function toArray()
    {
        return $this->_data;
    }

    /**
     * Returns an iteratable array with the model data.
     * @return ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->_data);
    }

    /**
     * Returns the number of data items in the model.
     * @return int
     */
    public function count()
    {
        return count($this->_data);
    }

    /**
     * Converts a method to a variable. A part of the string can be stripped by
     * providing a string as the second parameter.
     * @param string $name
     * @param string strip
     * @return string
     */
    private function _methodToVar($name, $strip = '')
    {
        if(!empty($strip)) {
    		$name = str_replace($strip, '', $name);
    	}
        
        // Split on capitals
        preg_match_all('/[A-Z]{1}[a-z_]*/', $name, $parts);

        // Construct the column name
        $var = implode('_', array_map('strtolower', $parts[0]));

        return $var;
    }

}
