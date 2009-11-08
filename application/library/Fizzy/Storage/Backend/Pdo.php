<?php
/**
 * Class Fizzy_Storage_Backend_Pdo
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

/** Fizzy_Storage_Backend_Abstract */
require_once 'Fizzy/Storage/Backend/Abstract.php';

/**
 * Storage backend based on the PDO extension.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
abstract class Fizzy_Storage_Backend_Pdo extends Fizzy_Storage_Backend_Abstract
{
    
    /**
     * Username for the database connection.
     * @var string
     */
    protected $_username = '';

    /**
     * Password for the database connection.
     * @var string
     */
    protected $_password = '';

    /**
     * The connection to the database.
     * @var PDO
     */
    protected $_connection = null;

    /** **/

    public function __construct($options = array())
    {
        parent::__construct($options);

        // Make a new PDO connection
        try {
            $this->_connection = new PDO($this->_dsn, $this->_username, $this->_password);
        }
        catch(PDOException $exception) {
            require_once 'Fizzy/Storage/Exception.php';
            throw new Fizzy_Storage_Exception($exception->getMessage());
        }
    }

    /**
     * Sets the username for the database connection.
     * @param string $username
     * @return Fizzy_Storage_Backend_Mysql
     */
    public function setUsername($username)
    {
        $this->_username = $username;
        return $this;
    }

    /**
     * Returns the username for the database.
     * @return string
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * Sets the password for the database connection.
     * @param string $password
     * @return Fizzy_Storage_Backend_Mysql
     */
    public function setPassword($password)
    {
        $this->_password = $password;
        return $this;
    }

    /**
     * Returns the password for the database connection.
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * Returns the PDO connection object.
     * @return PDO
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * Creates a prepared statement for the query.
     * @param string $query 
     * @return PDOStatement|null
     */
    protected function _createStatement($query)
    {
        $statement = $this->_connection->prepare($query);
        
        if(false === $statement) {
            require_once 'Fizzy/Storage/Exception.php';
            throw new Fizzy_Storage_Exception(implode(';', $this->_connection->errorInfo()));
        }

        return $statement;
    }

    /**
     * Executes a statement
     * @param PDOStatement $sql
     */
    protected function _executeStatement(PDOStatement $statement)
    {
        $success = $statement->execute();

        if(false === $success) {
            require_once 'Fizzy/Storage/Exception.php';
            throw new Fizzy_Storage_Exception(implode(';', $this->_connection->errorInfo()));
        }
        
        return $statement;
    }

    /* Implementation of Fizzy_Storage_Backend_Interface */

    /**
     * FetchAll for PDO enabled drivers
     * @see Fizzy_Storage_Backend_Interface
     */
    public function fetchAll($container)
    {
        // Prepare statement
        $statement = $this->_createStatement("SELECT * FROM `{$container}`");
        $executed = $this->_executeStatement($statement);

        $rows = $executed->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

    /**
     * @see Fizzy_Storage_Backend_Interface
     */
    public function fetchByIdentifier($container, $identifier)
    {
        $statement = $this->_createStatement("SELECT * FROM `{$container}` WHERE {$this->_identifierField} = :{$this->_identifierField}");
        $statement->bindValue(":{$this->_identifierField}", $identifier, PDO::PARAM_STR);
        $executed = $this->_executeStatement($statement);

        // Fetch row from the executed statement
        $row = $executed->fetch(PDO::FETCH_ASSOC);

        if(empty($row)) {
            return null;
        }

        $identifier = $row[$this->_identifierField];
        unset($row[$this->_identifierField]);
        
        return array($identifier => $row);
    }

    /**
     * @see Fizzy_Storage_Backend_Interface
     */
    public function fetchByColumn($container, array $columns)
    {
        if(0 === count($columns)) {
            require_once 'Fizzy/Storage/Exception.php';
            throw new Fizzy_Storage_Exception('No columns specified.');
        }

        $query = "SELECT * FROM `{$container}` WHERE ";
        $whereClause = array();
        foreach($columns as $column => $value) {
            $whereClause[] = "`{$column}` = :{$column}";
        }
        $query .= implode(' AND ', $whereClause);
        
        $statement = $this->_createStatement($query);
        foreach($columns as $column => $value) {
            $statement->bindValue(":{$column}", $value, PDO::PARAM_STR);
        }
        $executed = $this->_executeStatement($statement);

        $rows = $executed->fetchAll(PDO::FETCH_ASSOC);

        if(!empty($rows)) {
            return $rows;
        }

        return array();
    }

    /**
     * @see Fizzy_Storage_Backend_Interface
     */
    public function delete($container, $identifier)
    {
        // Check if the model is in persistence
        if(null === $identifier) {
            return true;
        }
        
        $statement = $this->_createStatement("DELETE FROM `{$container}` WHERE `{$this->_identifierField}` = :{$this->_identifierField}");
        $statement->bindValue(":{$this->_identifierField}", $identifier);
        $executed = $this->_executeStatement($statement);

        return true;
    }

    /**
     * @see Fizzy_Storage_Driver_Interface
     */
    public function persist($container, $data, $identifier = null)
    {
        $columns = array_keys($data);

        $setClause = array();
        foreach($columns as $column) {
            $setClause[$column] = "`{$column}` = :{$column}";
        }
        $setClause = implode(', ', $setClause);

        if (null !== $identifier) {
            $query = "UPDATE `{$container}` SET {$setClause} WHERE `{$this->_identifierField}` = :{$this->_identifierField}";
        }
        else {
            $query = "INSERT INTO `{$container}` SET {$setClause};";
        }

        $statement = $this->_createStatement($query);
        
        if(null !== $identifier) {
            $statement->bindValue(":{$this->_identifierField}", $identifier, PDO::PARAM_STR);
        }
        foreach($data as $column => $value) {
            $statement->bindValue(":{$column}", $value, PDO::PARAM_STR);
        }
        $executed = $this->_executeStatement($statement);

        // Return the id for the data set
        return $this->_connection->lastInsertId();
    }

}