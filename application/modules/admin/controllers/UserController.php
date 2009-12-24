<?php

class Admin_UserController extends Fizzy_SecuredController
{

    public function indexAction()
    {
        $storage = Zend_Registry::get('storage');
        $users = $storage->fetchAll('User');
        $this->view->users = $users;
        $this->renderScript('/user/list.phtml');
    }

    public function addAction()
    {
        $user = new User();
        $form = $this->_getForm($this->view->baseUrl('/fizzy/user/add'), $user);

        if($this->_request->isPost()) {
            if($form->isValid($_POST)) {
                $user->populate($form->getValues());
                $storage = Zend_Registry::get('storage');
                $storage->persist($user);

                $this->addSuccessMessage("User {$user->username} was successfully saved.");
                $this->_redirect('/fizzy/users', array('prependBase' => true));
            }
        }
        
        $this->view->form = $form;
        $this->renderScript('/user/form.phtml');
    }

    public function editAction()
    {
        $id = $this->_getParam('id', null);
        if(null === $id) {
            $this->_redirect('/fizzy/users', array('prependBase' => true));
        }

        $storage = Zend_Registry::get('storage');
        $user = $storage->fetchByID('User', $id);
        if(null === $user) {
            $this->addErrorMessage("User with ID {$id} could not be found.");
            $this->_redirect('/fizzy/users', array('prependBase' => true));
        }
        $form = $this->_getForm($this->view->baseUrl('/fizzy/user/edit/' . $user->getId()), $user);

        if($this->_request->isPost()) {
            if($form->isValid($_POST)) {
                $user->populate($form->getValues());
                $storage->persist($user);

                $this->addSuccessMessage("User <strong>{$user->username}</strong> was successfully saved.");
                $this->_redirect('/fizzy/users', array('prependBase' => true));
            }
        }

        $this->view->form = $form;
        $this->renderScript('user/form.phtml');
    }

    public function deleteAction()
    {
        $id = $this->_getParam('id', null);
        if(null !== $id) {
            $storage = Zend_Registry::get('storage');
            $user = $storage->fetchByID('User', $id);
            if(null !== $user) {
                $storage->delete($user);
                $this->addSuccessMessage("User {$user->username} was successfully deleted.");
            }
            
            $this->_redirect('/fizzy/users', array('prependBase' => true));
        }
    }

    protected function _getForm($action, User $user)
    {
        $formConfig = array (
            'action' => $action,
            'elements' => array (
                'id' => array (
                    'type' => 'hidden',
                    'options' => array (
                        'required' => false,
                        'value' => $user->getId()
                    )
                ),
                'username' => array (
                    'type' => 'text',
                    'options' => array (
                        'label' => 'Username',
                        'required' => true,
                        'value' => $user->username,
                        'validators' => array (
                            'usernameUnique'
                        )
                    )
                ),
                'password' => array (
                    'type' => 'password',
                    'options' => array (
                        'label' => 'Password',
                        'required' => true,
                        'validators' => array (
                            'passwordConfirm'
                        )
                    ),
                ),
                'password_confirm' => array (
                    'type' => 'password',
                    'options' => array (
                        'label' => 'Confirm password',
                        'required' => true,
                        'ignore' => true,
                    )
                ),
                'submit' => array (
                    'type' => 'submit',
                    'options' => array (
                        'label' => 'Save',
                        'ignore' => true
                    )
                ),
            ),
        );
        
        return new Fizzy_Form(new Zend_Config($formConfig));
    }

}