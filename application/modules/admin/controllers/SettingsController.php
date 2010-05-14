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
        $settingsByComponent = array();

        foreach ($settings as $setting) {
            if (isset($settingsByComponent[component])) {
                $settingsByComponent[$setting->component] = array();
            }
            $settingsByComponent[$setting->component][] = $setting;
        }

        $this->view->settings = $settingsByComponent;
    }
    
}