<?php
/**
 * Contact form controller
 **/
class ContactController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $form = $this->_getForm();
        if ($this->_request->isPost()) {
            if ($form->isValid($_POST)) {
                
                $contact = new Contact();
                $contact->name = $form->name->getValue();
                $contact->email = $form->email->getValue();
                $contact->body = $form->body->getValue();
                $contact->date = date('Y/m/d H:i:s', time());

                if (Setting::getKey('contact_log', 'contact')) {
                    $contact->save();
                }
                $contact->send();

                $this->renderScript('contact/thankyou.phtml');
                return;
            }
        }
        $this->view->form = $form;
    }

    protected function _getForm()
    {
        $form = new Zend_Form();
        $form->setAction($this->view->baseUrl('/contact'));
        $form->addElement(new Zend_Form_Element_Text('name', array(
            'label' => 'Name',
            'required' => true
        )));
        $form->addElement(new Zend_Form_Element_Text('email', array(
            'label' => 'E-mail',
            'required' => true
        )));
        $form->addElement(new Zend_Form_Element_Textarea('body', array(
            'label' => 'Question / feedback',
            'required' => true
        )));
        $form->addElement(new Zend_Form_Element_Submit('submit', array(
            'label' => 'Send',
        )));
        return $form;
    }
}
