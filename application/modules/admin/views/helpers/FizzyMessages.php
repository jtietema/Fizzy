<?php

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
                $output .= "\n</ul>\n</div>";
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