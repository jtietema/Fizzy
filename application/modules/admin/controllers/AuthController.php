<?php

class Admin_AuthController extends Fizzy_Controller
{

    public function loginAction()
    {
        $form = $this->_getForm();
        if($this->_request->isPost()) {
            if($form->isValid($_POST)) {
                $authAdapter = new Fizzy_Storage_AuthAdapter(
                    $form->username->getValue(), $form->password->getValue()
                );
                $result = Zend_Auth::getInstance()->authenticate($authAdapter);
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
        Zend_Auth::getInstance()->clearIdentity();
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
                    )
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

        return new Zend_Form(new Zend_Config($formConfig));
    }

}