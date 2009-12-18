<?php
/**
 * Class AdminUsersController
 * @package Fizzy
 * @subpackage Controller
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
require_once 'Fizzy/Controller.php';

class Admin_UsersController extends Fizzy_Controller
{
    protected $_storage = null;

    /**
     * Setup the storage, this is needed by all actions
     */
    public function _init()
    {
        parent::_init();
        $this->_storage = new Fizzy_Storage(Fizzy_Config::getInstance()->getSection('storage'));
    }

    public function listAction()
    {
        $users = $this->_storage->fetchAll('User');
        $this->getView()->users = $users;
        $this->getView()->setScript('admin/users.phtml');
    }

    /**
     * Add an user
     */
    public function addAction()
    {
        $this->getView()->message = '';
        $user = $this->getView()->user = new User();
        if ($this->getRequest()->getMethod() === Fizzy_Request::METHOD_POST){
            if ($_POST['password'] !== $_POST['password2']){
                $this->getView()->message = 'Passwords are not equal.';
                $user->setUsername($_POST['username']);
            } elseif (empty($_POST['password'])){
                $user->setUsername($_POST['username']);
                $this->getView()->message = 'Password can not be empty.';
            } elseif (empty($_POST['username'])){
                $this->getView()->message = 'Username can not be empty.';
            } else {
                $user->setUsername($_POST['username']);
                $user->setPassword(md5($_POST['password']));
                $this->_storage->persist($user);
                $this->_redirect('/admin/users');
            }
        }
        $this->getView()->action = 'add';
        $this->getView()->setScript('admin/user/form.phtml');
    }

    /**
     * Edit an user
     */
    public function editAction()
    {
        $this->getView()->message = '';
        $user = $this->getView()->user = $this->_storage->fetchByID('User', $this->_getParam('id'));
        if ($this->getRequest()->getMethod() === Fizzy_Request::METHOD_POST){
            if ($_POST['password'] === $_POST['password2'] && !empty($_POST['password']) && !empty($_POST['username'])){
                $user->setUsername($_POST['username']);
                $user->setPassword(md5($_POST['password']));
                $this->_storage->persist($user);
                $this->_redirect('/admin/users');
            } elseif (!empty($_POST['username']) && empty($_POST['password']) && empty($_POST['password2'])){
                $user->setUsername($_POST['username']);
                $this->_storage->persist($user);
                $this->_redirect('/admin/users');
            } else {
                $this->getView()->message = 'Username is empty or your passwords are not equal.';
            }
        }
        $this->getView()->action = 'edit/' . $user->getId();
        $this->getView()->setScript('admin/user/form.phtml');
    }

    /**
     * Remove an user
     */
    public function deleteAction()
    {
        $id = $this->_getParam('id');
        if(null !== $id)
        {
            $user = $this->_storage->fetchByID('User', $id);
            $this->_storage->delete($user);
        }

        $this->_redirect('/admin/users');
    }
}
