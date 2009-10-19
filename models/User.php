<?php
/**
 * User model.
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
require_once 'Fizzy/Model.php';

/**
 * User model, represents an user in the CMS
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
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
