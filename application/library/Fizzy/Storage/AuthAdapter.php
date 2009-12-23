<?php

/**
 * Authentication adapter for Fizzy_Storage.
 * Takes a model class and tries to authenticate with a username and password
 * against the model class.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Storage_AuthAdapter implements Zend_Auth_Adapter_Interface
{
    /**
     * Model class to use for authentication.
     * @var string
     */
    protected $_modelClass = 'User';

    /**
     * Username for authentication.
     * @var string
     */
    protected $_username = null;

    /**
     * Password for authentication
     * @var string
     */
    protected $_password = null;

    /** **/

    /**
     * Constructor for auth adapter based on Fizzy_Storage.
     * @param string $username
     * @param string $password
     */
    public function __construct($username = null, $password = null)
    {
        if(null !== $username) {
            $this->setUsername($username);
        }

        if(null !== $password) {
            $this->setPassword($password);
        }
    }

    /**
     * Set the model class to authenticate against.
     * @param string $modelClass
     * @return Fizzy_Storage_AuthAdapter
     */
    public function setModelClass($modelClass)
    {
        $this->_modelClass = $modeClass;

        return $this;
    }

    /**
     * Set the username used when authenticating
     * @param string $username
     * @return Fizzy_Storage_AuthAdapter
     */
    public function setUsername($username)
    {
        $this->_username = $username;

        return $this;
    }

    /**
     * Set the password used when authenticating.
     * @param string $password
     * @return Fizzy_Storage_AuthAdapter
     */
    public function setPassword($password)
    {
        $this->_password = $password;

        return $this;
    }

    /**
     * Autheticate against Fizzy_Storage
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        # Check the username
        $storage = Fizzy::getInstance()->getStorage();
        $users = $storage->fetchByField($this->_modelClass, array('username' => $this->_username));
        if(0 === count($users)) {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND,
                        null, array('Username not found.'));
        }
        else if(1 < count($users)) {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS,
                        null, array('Username did not match one instance.'));
        }
        $user =  array_shift($users);

        # Check the password
        $password = $this->_password;
        $encryption = strtolower($user->getEncryption());
        if(!empty($encryption) && 'plain' !== $encryption && in_array($encryption, array('md5', 'sha1'))) {
            $password = $encryption($password);
        }
        if($password !== $user->getPassword()) {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, 
                        null, array('Invalid password.'));
        }

        return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $user);
    }

}