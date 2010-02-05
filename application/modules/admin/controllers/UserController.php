<?php

class Admin_UserController extends Fizzy_SecuredController
{
    protected $_sessionNamespace = 'fizzy';
    protected $_redirect = '/fizzy/login';

    public function indexAction()
    {
        $query = Doctrine_Query::create()->from('User');
        $users = $query->fetchArray();
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
                $user->save();

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

        $query = Doctrine_Query::create()->from('User')->where('id = ?', $id);
        $user = $query->fetchOne();
        if(null === $user) {
            $this->addErrorMessage("User with ID {$id} could not be found.");
            $this->_redirect('/fizzy/users', array('prependBase' => true));
        }
        $form = $this->_getForm($this->view->baseUrl('/fizzy/user/edit/' . $user->id), $user);

        if($this->_request->isPost()) {
            if($form->isValid($_POST)) {
                $user->populate($form->getValues());
                $user->save();

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
            $query = Doctrine_Query::create()->from('User')->where('id = ?', $id);
            $user = $query->fetchOne();
            if(null !== $user) {
                $user->delete();
                $this->addSuccessMessage("User {$user->username} was successfully deleted.");
            }
            
            $this->_redirect('/fizzy/users', array('prependBase' => true));
        }
    }

    protected function _getForm($action, User $user)
    {
        $passwordConfirm = new Fizzy_Validate_EqualsField();
        $passwordConfirm->setOppositeField('password_confirm');
        $passwordConfirm->setFieldName('Password');

        $formConfig = array (
            'action' => $action,
            'elements' => array (
                'id' => array (
                    'type' => 'hidden',
                    'options' => array (
                        'required' => false,
                        'value' => $user->id
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
                            $passwordConfirm
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
