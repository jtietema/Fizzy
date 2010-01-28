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
                $mail = new Zend_Mail();
                $mail->setBodyText($form->feedback->getValue());
                $mail->setFrom($form->email->getValue(), $form->name->getValue());
                $application = Zend_Registry::get('config')->application->toArray();
                $mail->addTo($application['contactEmail']);
                $mail->send();
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
        $form->addElement(new Zend_Form_Element_Textarea('feedback', array(
            'label' => 'Question / feedback',
            'required' => true
        )));
        $form->addElement(new Zend_Form_Element_Submit('submit', array(
            'label' => 'Send',
        )));
        return $form;
    }
}
