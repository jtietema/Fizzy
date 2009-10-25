<?php

require_once 'SecureController.php';

class AdminUsersController extends SecureController
{

    public function listAction()
    {
        $config = Fizzy_Config::getInstance();
        $storageOptions = $config->getSection('storage');
        $this->_storage = new Fizzy_Storage($storageOptions);

        $users = $this->_storage->fetchAll('user');
        $this->getView()->users = $users;
        $this->getView()->setScript('admin/users.phtml');
    }

    /**
     * Makes sure the admin layout is selected.
     * @see Fizzy_Controller
     */
    public function after()
    {
        if($this->getRequest()->getAction() != 'login') {
            $this->getView()->setLayout('admin');
        }
    }
}