<?php
/**
 * Abstract Class Fizzy_AutoFill
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
 * Allows the extending class to be automatically filled through an options
 * array. The array is looped an for each key the class is checked for a
 * setter method. If the method is implemented it will be called with the
 * value as parameter.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
abstract class Fizzy_AutoFill
{

    /**
     * Constructor. Accepts an array of options.
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->setOptions($options);
    }

    /**
     * Set multiple options as array.
     * @param array $options
     */
    public function setOptions(array $options)
    {
        foreach($options as $option => $value) {
            $this->setOption($option, $value);
        }
    }

    /**
     * Checks if a setter method is defined for the name and calls it to set the
     * value.
     * @param string $name
     * @param mixed $value
     */
    public function setOption($name, $value)
    {   
        $methodName = 'set' . ucfirst($name);
        if(is_callable(array($this, $methodName))) {
            $this->$methodName($value);
        }
    }

}
