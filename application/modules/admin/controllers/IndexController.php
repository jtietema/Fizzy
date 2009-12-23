<?php

class Admin_IndexController extends Fizzy_SecuredController
{

    /**
     * Default action redirects to Pages overview.
     */
    public function indexAction()
    {
        $this->_redirect('/fizzy/pages');
    }

    public function configurationAction()
    {
        $this->view->config = Fizzy::getInstance()->getConfig()->toArray();
        $this->renderScript('configuration.phtml');
    }
    
}
