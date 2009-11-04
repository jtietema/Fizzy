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

/** Fizzy_AutoFill */
require_once 'Fizzy/AutoFill.php';

/** Fizzy_Storage_Backend_Interface */
require_once 'Fizzy/Storage/Backend/Interface.php';

/**
 * Storage backend based on the PDO extension.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
abstract class Fizzy_Storage_Backend_Pdo extends Fizzy_AutoFill
    implements Fizzy_Storage_Backend_Interface
{
    
    /**
     * DSN for the database connection.
     * @var string
     */
    protected $_dsn = null;

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

    /**
     * PDO errors
     * @var array
     */
    protected $_errors = array();

    /** **/

    public function __construct($options = array())
    {
        parent::__construct($options);

        // Make a new PDO connection
        try {
            $this->_connection = new PDO($this->_dsn, $this->_username, $this->_password);
        }
        catch(PDOException $exception) {
            // @todo log error
            //echo $exception->getMessage();
            //exit;
        }
    }

    /**
     * Sets the DSN for the database connection.
     * @param string $dsn
     * @return Fizzy_Storage_Backend_Pdo
     */
    public function setDsn($dsn)
    {
        $this->_dsn = $dsn;
        return $this;
    }

    /**
     * Returns the DSN for the database connection.
     * @return string
     */
    public function getDsn()
    {
        return $this->_dsn;
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
     * Checks if there were any errors.
     * @return boolean
     */
    public function hasErrors()
    {
        return (boolean) count($this->_errors);
    }

    /**
     * Returns the errors for the last executed statement.
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Creates a prepared statement for the query.
     * @param string $query 
     * @return PDOStatement|null
     */
    protected function _createStatement($query)
    {
        $statement = $this->_connection->prepare($query);
        
        if(false !== $statement) {
            return $statement;
        }

        $this->_errors = implode(';', $this->_connection->errorInfo());
        return null;
    }

    /**
     * Executes a statement
     * @param PDOStatement $sql
     */
    protected function _executeStatement(PDOStatement $statement)
    {
        $success = $statement->execute();

        if(false !== $success) {
            return $statement;
        }
        
        $this->_errors = array(implode(';', $statement->errorInfo()));
        return null;
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
        if($this->hasErrors()) {
            return array();
        }
        
        $executed = $this->_executeStatement($statement);

        if($this->hasErrors()) {
            return array();
        }

        $rows = $executed->fetchAll();
        return $rows;
    }

    /**
     * FetchOne for PDO enabled drivers
     * @see Fizzy_Storage_Backend_Interface
     */
    public function fetchOne($container, $identifier)
    {
        $statement = $this->_createStatement("SELECT * FROM `{$container}` WHERE id = :id");
        if($this->hasErrors()) {
            return array();
        }

        $statement->bindValue(':id', $identifier, PDO::PARAM_INT);
        $executed = $this->_executeStatement($statement);

        if($this->hasErrors()) {
            return null;
        }

        // Fetch rows from the executed statement
        $rows = $executed->fetch(PDO::FETCH_ASSOC);

        if(!empty($rows)) {
            return $rows;
        }

        return null;
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

        $statement = $this->_connection->prepare($query);
        foreach($columns as $column => $value) {
            $statement->bindValue(":{$column}", $value, PDO::PARAM_STR);
        }

        $executed = $this->_executeStatement($statement);

        if($this->hasErrors()) {
            return array();
        }

        $rows = $executed->fetchAll();

        if(!empty($rows)) {
            return $rows;
        }

        return array();
    }

    /**
     * @see Fizzy_Storage_Backend_Interface
     */
    public function delete($container, $identifierField, $identifierValue)
    {
        // Check if the model is in persistence
        if(null === $identifier) {
            return true;
        }

        $statement = $this->_createStatement("DELETE FROM `{$container}` WHERE `{$identifierField}` = :id");
        if($this->hasErrors()) {
            return false;
        }
        $statement->bindValue(':id', $identifierValue);

        $executed = $this->_executeStatement($statement);
        if($this->hasErrors()) {
            return false;
        }

        return true;
    }

    /**
     * @see Fizzy_Storage_Driver_Interface
     */
    public function persist($container, $data, $identifierField = null)
    {
        $columns = array_keys($data);
        $identifierValue = (null !== $identifierField && isset($data[$identifierField])) ? $data[$identifierField] : null;

        $setClause = array();
        foreach($columns as $column) {
            $setClause[$column] = "`{$column}` = :{$column}";
        }

        // Unset the id if the data set has one
        if(null !== $identifierValue) {
            unset($setClause[$identifierField]);
        }
        
        $setClause = implode(', ', $setClause);

        if ($identifierValue !== null) {
            $query = "UPDATE `{$container}` SET {$setClause} WHERE `{$identifierField}` = :{$identifierField}";
        }
        else {
            // Generate a new ID based on the current time as the id for the new model
            $data[$identifierField] = time();
            $query = "INSERT INTO `{$container}` SET `{$identifierField}` = :{$identifierField}, {$setClause};";
        }

        $statement = $this->_createStatement($query);
        if($this->hasErrors()) {
            return false;
        }

        foreach($data as $column => $value) {
            $statement->bindValue(":{$column}", $value, PDO::PARAM_STR);
        }
        $executed = $this->_executeStatement($statement);

        if($this->hasErrors()) {
            return false;
        }

        // Return the id for the data set
        return $data[$identifierValue];
    }

}