<?php

/** Zend_Controller_Plugin_Abstract */
require_once 'Zend/Controller/Plugin/Abstract.php';

class Fizzy_Layout_ControllerPlugin extends Zend_Controller_Plugin_Abstract
{

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $moduleName = $this->getRequest()->getModuleName();
        if(empty($moduleName)) {
            $moduleName = 'default';
        }

        $layoutPath = APPLICATION_PATH . '/modules/' . $moduleName . '/views/layouts/';

        $layout = Zend_Layout::getMvcInstance();
        
        $layout->setLayoutPath($layoutPath);
    }
    
}