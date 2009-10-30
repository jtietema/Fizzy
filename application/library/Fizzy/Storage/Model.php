<?php
/**
 * Abstract Class Fizzy_Storage_Model
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

/** Fizzy_Model */
require_once 'Fizzy/Model.php';

/**
 * Abstract Model class for storage based models.
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
abstract class Fizzy_Storage_Model extends Fizzy_Model
{
    /**
     * Type of the model
     *
     * @var string
     */
    protected $_type = null;

    /**
     * Identifiying column for this storage model.
     * @var string
     */
    protected $_identifier = 'id';

    /**
     * Get the unique identifier for this model.
     * @return mixed
     */
    public function getId()
    {
        $identifier = $this->_identifier;
        if(isset($this->$identifier)) {
            return $this->__get($this->_identifier);
        }
        
        return null;
    }

    /**
     * Set the unique identifier for this model. Can only be set once.
     * @param <type> $id
     */
    public function setId($id)
    {
        $identifier = $this->_identifier;
        if ($this->$identifier !== null) {
            require_once 'Fizzy/Exception.php';
            throw new Fizzy_Exception("Can't change models identifier.");
        }

        $this->$identifier = $id;
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

}
