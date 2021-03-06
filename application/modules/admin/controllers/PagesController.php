<?php
/**
 * Class Admin_PagesController
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

class Admin_PagesController extends Fizzy_SecuredController
{
    /**
     * Shows a list of pages managed by Fizzy.
     */
    public function indexAction()
    {
        $query = Doctrine_Query::create()->from('Page');
        $pages = $query->fetchArray();

        $this->view->pages = $pages;
    }

    /**
     * Adds a page.
     */
    public function addAction()
    {
        $page = new Page();
        $form = $this->_getForm($this->view->url('@admin_pages_add'), $page);

        if($this->_request->isPost()) {
            if($form->isValid($_POST)) {
                $page->populate($form->getValues());
                $page->save();

                $this->addSuccessMessage("Page {$page->title} was saved successfully.");
                $this->_redirect('@admin_pages');
            }
        }

        $this->view->page = $page;
        $this->view->form = $form;
        $this->renderScript('pages/form.phtml');
    }

    public function editAction()
    {
        $id = $this->_getParam('id', null);
        if(null === $id) {
            $this->_redirect('@admin_pages');
        }

        $query = Doctrine_Query::create()->from('Page')->where('id = ?', $id);
        $page = $query->fetchOne();
        if(null === $page) {
            $this->addErrorMessage("Page with ID {$id} could not be found.");
            $this->_redirect('@admin_pages');
        }
        $form = $this->_getForm($this->view->url('@admin_pages_edit?id=' . $page['id']), $page);

        if($this->_request->isPost()) {
            if($form->isValid($_POST)) {
                $page->populate($form->getValues());
                $page->save();

                $this->addSuccessMessage("Page \"<strong>{$page->title}</strong>\" was successfully saved.");
                $this->_redirect('@admin_pages');
            }
        }

        $this->view->page = $page;
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
            $query = Doctrine_Query::create()->from('Page')->where('id = ?', $id);
            $page = $query->fetchOne();
            if(null !== $page) {
                $page->delete();
                $this->addSuccessMessage("Page {$page->title} was successfully deleted.");
            }
        }

        $this->_redirect('@admin_pages');
    }

    /**
     * Builds the form for adding or editing a page.
     * @param string $action
     * @param Page $page
     * @return Zend_Form
     */
    protected function _getForm($action, $page)
    {
        $config = Zend_Registry::get('config');

        $formConfig = array (
            'action' => $action,
            'elements' => array (
                'id' => array (
                    'type' => 'hidden',
                    'options' => array (
                        'required' => false,
                        'value' => $page['id'],
                    )
                ),
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
                        'attribs' => array('style' => 'width: 100%;'),
                    )
                ),
                'template' => array (
                    'type' => 'select',
                    'options' => array (
                        'label' => 'Template',
                        'required' => true,
                        'multiOptions' => $this->_fetchFiles($config->paths->templatePath),
                        'value' => $page->getTemplate(),
                        'description' => 'You can select a different template to structure the text.'
                    )
                ),
                'layout' => array (
                    'type' => 'select',
                    'options' => array (
                        'label' => 'Layout',
                        'required' => true,
                        'multiOptions' => $this->_fetchFiles($config->paths->layoutPath, false),
                        'value' => $page->getLayout(),
                        'description' => 'You can select a different layout to render the structured text in.'
                    )
                ),
                'homepage' => array (
                    'type' => 'checkbox',
                    'options' => array (
                        'label' => 'Is Homepage',
                        'required' => false,
                        'value' => (int) $page->homepage,
                        'checked' => ((boolean) $page->homepage) ? 'checked' : '',
                        'description' => 'Check this box to make this page the default.'
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

        $form->template->addDecorator('Description');
        $form->layout->addDecorator('Description');

        return $form;
    }

    /**
     * Fetches a list of files from a given directory
     * 
     * @param string $dir
     * @return array
     */
    protected function _fetchFiles($dir, $keepExtension = true)
    {
        $files = array();
        $dir = new DirectoryIterator($dir);
        foreach ($dir as $file) {
            if($file->isDot()) {
                continue;
            }
            $pieces = explode('.', $file->getFilename());
            if ($keepExtension) {
                $files[$file->getFilename()] = $pieces[0];
            } else {
                $files[$pieces[0]] = $pieces[0];
            }
        }
        return $files;
    }
    
}
