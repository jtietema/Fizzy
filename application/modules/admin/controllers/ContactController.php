<?php

class Admin_ContactController extends Fizzy_SecuredController
{
    protected $_sessionNamespace = 'fizzy';
    protected $_redirect = '/fizzy/login';

    public function indexAction()
    {
        $this->view->messages = Doctrine_Core::getTable('Contact')->findAll();
    }

    public function showAction()
    {
        $id = $this->_getParam('id', null);
        if(null === $id) {
            $this->_redirect('/fizzy/contact', array('prependBase' => true));
        }

        $query = Doctrine_Query::create()->from('Contact')->where('id = ?', $id);
        $message = $query->fetchOne();
        if(null === $message) {
            $this->addErrorMessage("Message with ID {$id} could not be found.");
            $this->_redirect('/fizzy/contact', array('prependBase' => true));
        }

        $this->view->message = $message;
    }

    public function deleteAction()
    {
        $id = $this->_getParam('id', null);
        if(null === $id) {
            $this->_redirect('/fizzy/contact', array('prependBase' => true));
        }

        $query = Doctrine_Query::create()->from('Contact')->where('id = ?', $id);
        $message = $query->fetchOne();
        if(null === $message) {
            $this->addErrorMessage("Message with ID {$id} could not be found.");
            $this->_redirect('/fizzy/contact', array('prependBase' => true));
        }

        $success = $message->delete();
        if ($success) {
            $this->addSuccessMessage("Message was deleted");
        } else {
            $this->addErrorMessage("Message with ID {$id} could not be deleted.");
        }

        $this->_redirect('/fizzy/contact', array('prependBase' => true));
    }
    
}