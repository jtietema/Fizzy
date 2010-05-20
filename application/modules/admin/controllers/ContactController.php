<?php
/**
 * Class Admin_ContactController
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