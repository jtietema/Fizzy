<?php

require_once 'SecureController.php';

class AdminPagesController extends SecureController
{

    /**
     * Shows a list of pages.
     */
    public function listAction()
    {
        $config = Fizzy_Config::getInstance();
        $storageOptions = $config->getConfiguration('storage');
        $this->_storage = new Fizzy_Storage($storageOptions);

        $pages = $this->_storage->fetchAll('page');
        $this->getView()->pages = $pages;
        $this->getView()->setScript('admin/pages.phtml');
    }

    /**
     * Adds a page to the cms.
     */
    public function addAction()
    {
        $this->getView()->setScript('admin/page/form.phtml');
    }

    /**
     * Makes sure the admin layout is selected.
     */
    public function after()
    {
        $this->getView()->setLayout('admin.phtml');
    }

}