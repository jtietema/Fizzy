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
 * @copyright Copyright (c) 2009-2010 Voidwalkers (http://www.voidwalkers.nl)
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
        $this->view->settings = $settings;
    }

    /**
     * Update setting action called through an ajax request
     */
    public function ajaxUpdateAction()
    {
        // Disable view & layout output
        $this->_disableDisplay();
        // Alwasy set response type to json
        $this->_response->setHeader('Content-Type', 'application/json', true);

        // Try to get the data from the request body
        try {
            $data = Zend_Json::decode($this->_request->getRawBody(), Zend_Json::TYPE_OBJECT);
        } catch(Zend_Json_Exception $exception) {
            $this->_response->setHttpResponseCode(500);
            $this->_response->setBody(Zend_Json::encode(array(
                'status' => 'error',
                'message' => 'data decoding failed'
            )));
            return;
        }

        // Retrieve the setting
        $setting = Setting::getKey($data->settingKey, $data->component);
        if (null === $setting) {
            $this->_response->setHttpResponseCode(404);
            $this->_response->setBody(Zend_Json::encode(array(
                'status' => 'error',
                'message' => 'setting key/component pair not found'
            )));
            return;
        }

        // Update the setting
        $setting->value = $data->value;
        $success = $setting->trySave();
        if (false === $success) {
            $this->_response->setHttpResponseCode(500);
            $this->_response->setBody(Zend_Json::encode(array(
                'status' => 'error',
                'message' => 'saving failed due to validation errors'
            )));
            return;
        }

        // Return success response
        $this->_response->setHttpResponseCode(200);
        $this->_response->setBody(Zend_Json::encode(array(
            'status' => 'success',
            'message' => 'setting saved'
        )));
    }
    
}