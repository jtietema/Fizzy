<?php
require_once 'Fizzy/Storage/Interface.php';
require_once 'Fizzy/Storage/Exception/SQLiteError.php';

/**
 * Storage backend to SQLite
 *
 * @author jeroen
 */
class Fizzy_Storage_SQLite implements Fizzy_Storage_Interface
{
    protected $_pdo = null;

    public function __construct($dsn)
    {
        $this->_pdo = new PDO($dsn);
    }

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

    public function remove(Fizzy_Model $model)
    {
        $type = $model->getType();
        $stmt = $this->_pdo->prepare("DELETE FROM $type WHERE id = :id");
        $stmt->bindValue(':id', $model->getId(), PDO::PARAM_INT);
        $stmt->execute();
    }

    public function fetchOne($type, $uid)
    {
        $stmt = $this->_pdo->prepare("SELECT * FROM $type WHERE id = :id");
        $stmt->bindValue(':id', $uid, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Fetches all rows of $type.
     *
     * @TODO: implement some sort of ordering
     *
     * @param string $type
     * @return array
     */
    public function fetchAll($type)
    {
        $stmt = $this->_pdo->prepare("SELECT * FROM $type");
        $stmt->execute();
        return $stmt->fetchAll();
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
