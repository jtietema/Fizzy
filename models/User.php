<?php

require_once 'Fizzy/Model.php';

class User extends Fizzy_Model
{

    protected $_username;
    protected $_password;

    public function getUsername()
    {
        return $this->_username;
    }

    public function setUsername($username)
    {
        $this->_username = $username;
    }

    public function getPassword()
    {
        return $this->_password;
    }

    public function setPassword($password)
    {
        $this->_password = $password;
    }

    public function toArray() {
        return array(
            'username' => $this->_username,
            'password' => $this->_password
        );
    }
    public function populate($array) {
        $this->_username = $array['username'];
        $this->_password = $array['password'];
        $this->_id = $array['id'];
    }
}
