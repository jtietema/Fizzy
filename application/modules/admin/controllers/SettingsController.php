<?php
/**
 * Class Admin_SettingsController
 * @category Fizzy
 * @package Admin
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