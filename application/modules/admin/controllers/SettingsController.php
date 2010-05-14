<?php
/**
 * Class Admin_SettingsController
 * 
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */

class Admin_SettingsController extends Fizzy_SecuredController
{
    protected $_sessionNamespace = 'fizzy';
    protected $_redirect = '/fizzy/login';

    public function indexAction()
    {
        $settings = Setting::getAll();
        $this->view->settings = $settings;
    }
    
}