<?php
/**
 * Class Admin_AuthController
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

class Admin_AuthController extends Fizzy_Controller
{

    public function loginAction()
    {
        $form = $this->_getForm();
        if($this->_request->isPost()) {
            if($form->isValid($_POST)) {
                $authAdapter = new Fizzy_Doctrine_AuthAdapter(
                    $form->username->getValue(), $form->password->getValue()
                );
                $auth = Zend_Auth::getInstance();
                $auth->setStorage(new Zend_Auth_Storage_Session('fizzy'));
                $result = $auth->authenticate($authAdapter);
                if($result->isValid()) {
                    $this->_redirect('/fizzy', array('prependBase' => true));
                }
                $messages = $result->getMessages();
                $this->addErrorMessage(array_shift($messages));
                $this->_redirect('/fizzy/login', array('prependBase' => true));
            }
        }
        $this->view->form = $form;
        $this->renderScript('login.phtml');
    }

    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session('fizzy'));
        $auth->clearIdentity();
        $this->_redirect('/fizzy', array('prependBase' => true));
    }

    protected function _getForm()
    {
        $formConfig = array (
            'elements' => array (
                'username' => array (
                    'type' => 'text',
                    'options' => array (
                        'label' => 'Username',
                        'required' => true
                    )
                ),
                'password' => array (
                    'type' => 'password',
                    'options' => array (
                        'label' => 'Password',
                        'required' => true
                    ),
                ),
                'submit' => array (
                    'type' => 'submit',
                    'options' => array (
                        'label' => 'Login',
                        'ignore' => true
                    )
                )
            )
        );

        return new Fizzy_Form(new Zend_Config($formConfig));
    }

    public function postDispatch()
    {
        $this->_helper->layout->setLayout('login');
    }

}
