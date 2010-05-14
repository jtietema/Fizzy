<?php
/**
 * Class Contact
 * @package Fizzy
 * @category Model
 */

/**
 * Model class for contact requests
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Contact extends BaseContact
{

    /**
     * Sends the contact request to the configured contact email address.
     *
     * @throws Exception when contact email is not configured
     */
    public function send()
    {

        $contactEmail = Setting::getKey('address', 'contact');

        if (null === $contactEmail) {
            throw new Exception('Contact email not configured');
        }
        
        $mail = new Zend_Mail();
        $mail->setSubject('Contact request')
             ->setFrom($this->email, $this->name)
             ->setBodyText($this->body)
             ->addTo($contactEmail)
             ->send();
    }

}