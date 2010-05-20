<?php
/**
 * Class Admin_View_Helper_FizzyMessages
 * @category Fizzy
 * @package Admin_View
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
 * Checks the session for message placed by Fizzy.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Admin_View_Helper_FizzyMessages extends Zend_View_Helper_Abstract
{

    protected $_translator = null;
    /**
     * A formatted list of status messages from Fizzy.
     * @return string
     */
    public function fizzyMessages($translator = null)
    {
        $this->_translator = $translator;
        $output = '';
        $messages = $this->_getMessages();

        if(0 < $messages) {

            $statusMessages = array();
            # Filter out the Fizzy messages
            foreach($messages as $message) {
                if(is_array($message)) {
                    $statusMessages[$message['type']][] = $this->_translateMessage($message['message']);
                }
            }

            # Format the messages for the statusses
            foreach($statusMessages as $type => $messages) {
                $output .= "<div class=\"message {$type}\">\n<ul>\n";
                foreach($messages as $message) {
                    $output .= "<li>{$message}</li>\n";
                }

                $output .= "\n</ul>\n";
                $output .= "</div>";
            }
        }

        return $output;
    }

    /**
     * Gets the messages array from the Flash Messenger controller plugin.
     * @return array
     */
    protected function _getMessages()
    {
        return Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger')->getMessages();
    }

    /**
     * Tries to translate a message with the provided translator.
     * @param string $message
     * @return string
     */
    protected function _translateMessage($message)
    {
        if(null !== $this->_translator) {
            $message = $this->_translator->_($message);
        }

        return $message;
    }
    
}