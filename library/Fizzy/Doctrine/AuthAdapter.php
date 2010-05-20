<?php
/**
 * Class Fizzy_Doctrine_AuthAdapter
 * @category Fizzy
 * @package Fizzy_Doctrine
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
 * Auth adapter for Doctrine backend
 **/
class Fizzy_Doctrine_AuthAdapter implements Zend_Auth_Adapter_Interface
{
    protected $_username = null;
    protected $_password = null;
    protected $_modelClass = 'User';

    public function __construct($username = null, $password = null)
    {
        if (null !== $username) {
            $this->setUsername($username);
        }

        if (null !== $password) {
            $this->setPassword($password);
        }
    }

    public function setUsername($username)
    {
        $this->_username = $username;

        return $this;
    }

    public function setPassword($password)
    {
        $this->_password = $password;

        return $this;
    }

    public function setModelClass($modelClass)
    {
        $this->_modelClass = $modelClass;

        return $this;
    }

    public function authenticate()
    {
        # Check the username
        $query = Doctrine_Query::create()->from($this->_modelClass)->where('username = ?', $this->_username);
        $users = $query->fetchArray();
        if(0 === count($users)) {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND,
                        null, array('Username not found.'));
        }
        else if(1 < count($users)) {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS,
                        null, array('Username did not match one instance.'));
        }
        $user =  $users[0];

        # Check the password
        $password = $this->_password;
        $encryption = strtolower($user['encryption']);
        if(!empty($encryption) && 'plain' !== $encryption && in_array($encryption, array('md5', 'sha1'))) {
            $password = $encryption($password);
        }
        if($password !== $user['password']) {
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, 
                        null, array('Invalid password.'));
        }

        return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $user);
    }
}
