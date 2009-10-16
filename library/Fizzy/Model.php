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
 * Abstract Model class that all models should implement.
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
abstract class Fizzy_Model
{
    /**
     * Uniek id for this models content.
     * 
     * @var mixed
     */
    protected $_id = null;

    /**
     * Type of the model
     *
     * @var string
     */
    protected $_type = null;

    /**
     * Get the unieke id of this model.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        if ($this->_id !== null)
            throw new Fizzy_Exception("Can't change model ID's.");

        $this->_id = $id;
    }

    /**
     * Return the type of this model.
     *
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * A model should be able to populate itself with data.
     * The data will be in the form off field => value
     *
     * @param array $array
     */
    public abstract function populate($array);

    /**
     * Returns an array with fieldnames as keys and their values as values.
     * 
     * @return array
     */
    public abstract function toArray();
}
