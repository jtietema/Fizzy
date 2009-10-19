<?php
/**
 * Class Fizzy_Storage_SQLite
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

/** Fizzy_Storage_Interface */
require_once 'Fizzy/Storage/Interface.php';

/**
 * Storage backend to SQLite
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
class Fizzy_Storage_SQLite implements Fizzy_Storage_Interface
{
    protected $_pdo = null;

    public function __construct($dsn)
    {
        $this->_pdo = new PDO($dsn);
    }

    /**
     * @see Fizzy_Interface
     */
    public function persist(Fizzy_Model $model)
    {
        $type = $model->getType();
        $fields = $model->toArray();

        $keys = array_keys($fields);
        $valueKeys = array_map(array($this, '_addColon'), $keys);
        
        $implodedKeys = implode(',', $keys);
        $implodedValueKeys = implode(',', $valueKeys);

        if ($model->getId() !== null)
        {
            // the model exists and should be saved

            $keyValue = array_combine($keys, $valueKeys);
            $set = '';
            foreach ($keyValue as $key => $value)
            {
                $set .= $key . ' = ' . $value . ', ';
            }
            $set = substr($set, 0, strlen($set)-2); // strip the last ', '

            $stmt = $this->_pdo->prepare("UPDATE $type SET $set WHERE id = :id");

            if (!$stmt) {
                require_once 'Fizzy/Storage/Exception/SQLiteError.php';
                throw new Fizzy_Storage_Exception_SQLiteError(implode(' | ', $this->_pdo->errorInfo()));
            }

            $stmt->bindValue(':id', $model->getId(), PDO::PARAM_INT);
        }
        else
        {
            // the model is new and should be added
            $stmt = $this->_pdo->prepare("INSERT INTO $type (id, $implodedKeys) VALUES (:id, $implodedValueKeys)");

            // if the table should not exist
            if (!$stmt && $this->_pdo->errorCode() === "HY000")
            {
                // create the table
                $this->_createTable($model);
                // and try again
                return $this->persist($model);
            }
            elseif (!$stmt)
            {
                require_once 'Fizzy/Storage/Exception/SQLiteError.php';
                throw new Fizzy_Storage_Exception_SQLiteError($this->_pdo->errorInfo());
            }

            // generate an id and set it
            $id = time();
            $stmt->bindValue(':id', $id);
            $model->setId($id);
        }

        foreach ($fields as $column => $value)
        {
            $stmt->bindValue(':' . $column, $value);
        }
        
        $stmt->execute();
        return $model;
    }

    /**
     * @see Fizzy_Interface
     */
    public function remove(Fizzy_Model $model)
    {
        $type = $model->getType();
        $stmt = $this->_pdo->prepare("DELETE FROM $type WHERE id = :id");
        $stmt->bindValue(':id', $model->getId(), PDO::PARAM_INT);
        $stmt->execute();
    }

    /**
     * @see Fizzy_Interface
     */
    public function fetchOne($type, $uid)
    {
        $stmt = $this->_pdo->prepare("SELECT * FROM $type WHERE id = :id");
        $stmt->bindValue(':id', $uid, PDO::PARAM_INT);
        $stmt->execute();

        $array = $stmt->fetch(PDO::FETCH_ASSOC);

        // check if there are results, if not return 
        if (empty($array))
            return null;
        return $array;
    }

    /**
     * @see Fizzy_Interface
     * @TODO: implement some sort of ordering
     */
    public function fetchAll($type)
    {
        $stmt = $this->_pdo->prepare("SELECT * FROM $type");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @see Fizzy_Interface
     */
    public function fetchColumn($type, $column, $value)
    {
        $stmt = $this->_pdo->prepare("SELECT * FROM $type WHERE $column = :value");
        $stmt->bindValue(':value', $value);
        $stmt->execute();

        $array = $stmt->fetch(PDO::FETCH_ASSOC);

        // check if there are results, if not return
        if (empty($array))
            return null;
        return $array;
    }

    /**
     * Adds a colon to the given string
     *
     * @param string $item
     * @return string
     */
    protected function _addColon($item)
    {
        return ':' . $item;
    }

    /**
     * Creates a table for the given model
     * 
     * @param Fizzy_Model $model
     */
    protected function _createTable(Fizzy_Model $model)
    {
        $type = $model->getType();
        $columns = implode( ',', array_keys( $model->toArray() ) );
        
        $stmt = $this->_pdo->prepare("CREATE TABLE $type (id, $columns)");
        $stmt->execute();
    }
}
