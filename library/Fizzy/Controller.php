<?php
/**
 * Class Fizzy_Controller
 * @package Fizzy
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

/** Zend_Controller_Action */
require_once 'Zend/Controller/Action.php';

/**
 * Controller class for Fizzy.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Controller extends Zend_Controller_Action
{
    /**
     * Flash messenger helper instance
     * @var Zend_View_Helper_FlashMessenger
     */
    protected $_flashMessenger = null;

    /**
     * Disables the view renderer and layout
     */
    protected function _disableDisplay()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
    }

    /**
     * Adds a flash message to the session.
     * @todo move to controller plugin
     * @param string $message
     * @param string $type Defaults to 'info'
     */
    protected function addMessage($message, $type = 'info')
    {
        $this->_getFlashMessenger()->addMessage(array(
            'message' => $message,
            'type' => strtolower($type)
        ));
    }

    /**
     * Adds a message of type 'info' to the session's flash messenger.
     * @todo move to controller plugin
     * @param string $message
     */
    protected function addInfoMessage($message)
    {
        $this->addMessage($message, 'info');
    }

    /**
     * Adds a message of type 'success' to the session's flash messenger.
     * @todo move to controller plugin
     * @param string $message
     */
    protected function addSuccessMessage($message)
    {
        $this->addMessage($message, 'success');
    }

    /**
     * Adds a message of type 'warning' to the session's flash messenger.
     * @todo move to controller plugin
     * @param string $message
     */
    protected function addWarningMessage($message)
    {
        $this->addMessage($message, 'warning');
    }

    /**
     * Adds a message of type 'error' to the session's flash messenger.
     * @todo move to controller plugin
     * @param string $message
     */
    protected function addErrorMessage($message)
    {
        $this->addMessage($message, 'error');
    }

    /**
     * Gets the flash messenger instance for this controller. Initializes the
     * helper from the helper broker if no active instance is found.
     * @return Zend_View_Helper_FlashMessenger
     */
    protected function _getFlashMessenger()
    {
        if(null === $this->_flashMessenger) {
            $this->_flashMessenger = $this->_helper->getHelper('flashMessenger');
        }

        return $this->_flashMessenger;
    }

}
