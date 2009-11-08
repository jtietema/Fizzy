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
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
abstract class Fizzy_Storage_Model extends Fizzy_Model
{
    /**
     * Container name for the model
     * @var string
     */
    protected $_containerName = null;

    /**
     * Identifier value for the model.
     * @var mixed
     */
    protected $_identifier = null;

    /**
     * Get the unique identifier for this model.
     * @return mixed
     */
    public function getId()
    {
        return $this->_identifier;
    }

    /**
     * Set the unique identifier for this model. Can only be set once.
     * @param <type> $id
     */
    public function setId($id)
    {
        if(null !== $this->_identifier) {
            require_once 'Fizzy/Storage/Exception.php';
            throw new Fizzy_Storage_Exception("Can't change models identifier.");
        }

        $this->_identifier = $id;
    }

    /**
     * Returns the name for the container to store the model.
     * @return string
     */
    public function getContainerName()
    {
        if(null === $this->_containerName) {
            require_once 'Fizzy/Storage/Exception.php';
            throw new Fizzy_Storage_Exception('Model ' . get_class($this) . ' does not have a container name.');
        }
        
        return $this->_containerName;
    }

}
