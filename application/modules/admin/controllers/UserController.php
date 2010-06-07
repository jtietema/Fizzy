<?php
/**
 * Class Admin_UserController
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

class Admin_UserController extends Fizzy_SecuredController
{

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
        $form = $this->_getForm($this->view->url('@admin_users_add'), $user);
        $form->password->setRequired(true);
        $form->password_confirm->setRequired(true);

        if($this->_request->isPost()) {
            if($form->isValid($_POST)) {
                $user->populate($form->getValues());
                $user->save();

                $this->addSuccessMessage("User {$user->username} was successfully saved.");
                $this->_redirect('@admin_users');
            }
        }

        $this->view->user = $user;
        $this->view->form = $form;
        $this->renderScript('/user/form.phtml');
    }

    public function editAction()
    {
        $id = $this->_getParam('id', null);
        if(null === $id) {
            $this->_redirect('@admin_users');
        }

        $query = Doctrine_Query::create()->from('User')->where('id = ?', $id);
        $user = $query->fetchOne();
        if(null === $user) {
            $this->addErrorMessage("User with ID {$id} could not be found.");
            $this->_redirect('@admin_users');
        }
        $form = $this->_getForm($this->view->url('@admin_users_edit?id=' . $user->id), $user);

        if($this->_request->isPost()) {
            if($form->isValid($_POST)) {
                $user->populate($form->getValues());
                $user->save();

                $this->addSuccessMessage("User <strong>{$user->username}</strong> was successfully saved.");
                $this->_redirect('@admin_users');
            }
        }

        $this->view->user = $user;
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
            
            $this->_redirect('@admin_users');
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
                'displayname' => array(
                    'type' => 'text',
                    'options' => array(
                        'label' => 'Display name',
                        'required' => true,
                        'value' => $user->displayname
                    )
                ),
                'password' => array (
                    'type' => 'password',
                    'options' => array (
                        'label' => 'Password',
                        'validators' => array (
                            $passwordConfirm
                        )
                    ),
                ),
                'password_confirm' => array (
                    'type' => 'password',
                    'options' => array (
                        'label' => 'Confirm password',
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
