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

    public function addblockAction()
    {
        $id = $this->_getParam('id', null);
        $type = $this->_getParam('type', null);
        if (null === $id || null === $type) {
            $this->_redirect('@admin_pages');
        }
        Block::createBlock($id, $type);
        $this->_redirect($this->view->url('@admin_pages_edit?id='.$id, array('prependBase' => false)));
    }

    public function deleteblockAction()
    {
    	$pageId = $this->_getParam('id', null);
	    $blockId = $this->_getParam('blockId', null);
	    if ($pageId === null || $blockId === null) {
		    $this->_redirect('@admin_pages');
	    }
        Block::removeBlock($blockId);
        $this->_redirect($this->view->url('@admin_pages_edit?id='.$pageId, array('prependBase' => false)));
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

                // save all the blocks
                foreach ($form->blocks->getElements() as $element) {
                    list($block, $type, $id) = explode('_', $element->getName());
                    $model = Block::loadModel($type, $id);
                    $model->setValue($element->getValue());
                    $model->save();
                }
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
        // create blocks subform
        $blocksForm = new Zend_Form_SubForm();
        foreach ($page->Blocks as $block){
            $element = $block->getFormElement();
	    $element->addDecorator(new Fizzy_Decorator_BlockOperations(
		    array(
			'baseUrl' => $this->view->baseUrl(),
			'blockId' => $block->id,
            'pageId' => $page->id
            )
        ));
	    $blocksForm->addElement($element);
        }

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
                'newblock' => array(
                    'type' => 'select',
                    'options' => array(
                        'label' => 'Add a block',
                        'required' => false,
                        'ignore' => true,
                        'multiOptions' => array(
                            '0' => '-- select block type --',
                            'map' => 'Google Map',
                            'image' => 'Image',
                            'richtext' => 'Richtext',
                            'textarea' => 'Textarea',
                        ),
                        'onchange' => "window.location='{$this->view->url('@admin_pages_addblock?id='.$page->id.'&type=')}' + '/' + this.value;"
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
        $form->addSubForm($blocksForm, 'blocks');

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
