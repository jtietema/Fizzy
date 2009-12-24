<?php

class Admin_PagesController extends Fizzy_SecuredController
{

    /**
     * Shows a list of pages managed by Fizzy.
     */
    public function indexAction()
    {
        $storage = Zend_Registry::get('storage');
        $pages = $storage->fetchAll('Page');
        $this->view->pages = $pages;
    }

    /**
     * Adds a page.
     */
    public function addAction()
    {
        $page = new Page();
        $form = $this->_getForm($this->view->baseUrl('/fizzy/pages/add'), $page);

        if($this->_request->isPost()) {
            if($form->isValid($_POST)) {
                $page->populate($form->getValues());
                $storage = Zend_Registry::get('storage');
                $storage->persist($page);
                
                $this->addSuccessMessage("Page {$page->title} was saved successfully.");
                $this->_redirect('/fizzy/pages', array('prependBase' => true));
            }
        }

        $this->view->form = $form;
        $this->renderScript('pages/form.phtml');
    }

    public function editAction()
    {
        $id = $this->_getParam('id', null);
        if(null === $id) {
            $this->_redirect('/fizzy/pages', array('prependBase' => true));
        }

        $storage = Zend_Registry::get('storage');
        $page = $storage->fetchByID('Page', $id);
        if(null === $page) {
            $this->addErrorMessage("Page with ID {$id} could not be found.");
            $this->_redirect('/fizzy/pages', array('prependBase' => true));
        }
        $form = $this->_getForm($this->view->baseUrl('/fizzy/pages/edit/' . $page->getId()), $page);

        if($this->_request->isPost()) {
            if($form->isValid($_POST)) {
                $page->populate($form->getValues());
                $storage->persist($page);

                $this->addSuccessMessage("Page \"<strong>{$page->title}</strong>\" was successfully saved.");
                $this->_redirect('/fizzy/pages', array('prependBase' => true));
            }
        }

        $this->view->form = $form;
        $this->renderScript('pages/form.phtml');
    }

    /**
     * Delete a page
     */
    public function deleteAction()
    {
        $id = $this->_getParam('id', null);
        if(null !== $id) {
            $storage = Zend_Registry::get('storage');
            $page = $storage->fetchByID('Page', $id);
            if(null !== $page) {
                $storage->delete($page);
                $this->addSuccessMessage("Page {$page->title} was successfully deleted.");
            }
        }

        $this->_redirect('/fizzy/pages', array('prependBase' => true));
    }

    /**
     * Builds the form for adding or editing a page.
     * @param string $action
     * @param Page $page
     * @return Zend_Form
     */
    protected function _getForm($action, $page)
    {
        $formConfig = array (
            'action' => $action,
            'elements' => array (
                'title' => array (
                    'type' => 'text',
                    'options' => array (
                        'label' => 'Title',
                        'required' => true,
                        'value' => $page->title,
                    )
                ),
                'slug' => array (
                    'type' => 'text',
                    'options' => array (
                        'label' => 'Slug',
                        'required' => true,
                        'value' => $page->slug,
                        'filters' => array (
                            'slugify'
                        ),
                        'validators' => array (
                            'slugUnique'
                        )
                    )
                ),
                'body' => array (
                    'type' => 'wysiwyg',
                    'options' => array (
                        'label' => 'Body',
                        'required' => true,
                        'value' => $page->body,
                    )
                ),
                'template' => array (
                    'type' => 'select',
                    'options' => array (
                        'label' => 'Template',
                        'required' => true,
                        'multiOptions' => array('default' => 'Default'),
                        'value' => $page->getTemplate(),
                    )
                ),
                'layout' => array (
                    'type' => 'select',
                    'options' => array (
                        'label' => 'Layout',
                        'required' => true,
                        'multiOptions' => array('default' => 'Default'),
                        'value' => $page->getLayout(),
                    )
                ),
                'homepage' => array (
                    'type' => 'checkbox',
                    'options' => array (
                        'label' => 'Is Homepage',
                        'required' => false,
                        'value' => (int) $page->homepage,
                        'checked' => ((boolean) $page->homepage) ? 'checked' : ''
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

        $form = new Fizzy_Form();
        $form->setOptions($formConfig);
        return $form;
    }
    
}