<?php
/**
 * Class Admin_PagesController
 * @package Fizzy
 * @subpackage Controller
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

/** Fizzy_SecuredController */
require_once 'Fizzy/SecuredController.php';

/**
 * Admin controller for managing the pages
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Admin_PagesController extends Fizzy_SecuredController
{

    /**
     * Shows a list of pages.
     */
    public function listAction()
    {
        $storage = Zend_Registry::get('storage');

        $pages = $storage->fetchAll('Page');
        $this->view->pages = $pages;
        $this->renderScript('admin/pages.phtml');
    }

    /**
     * Adds a page to the cms.
     */
    public function addAction()
    {
        $config = Zend_Registry::get('config');
        $application = $config->application->toArray();
        $paths = $config->paths->toArray();
        
        // Get template and layout paths
        $templatePaths = $paths['templates'];
        $layoutPaths = $paths['layouts'];

        // Get available templates and layouts
        $templates = $this->_getViewScripts($templatePaths);
        $layouts = $this->_getViewScripts($layoutPaths);
        
        // Unset default template and layout
        unset($templates[$application['defaultTemplate']], $layouts[$application['defaultLayout']]);

        if($this->_request->isPost()) {
            $page = new Page();
            $page->setTitle($_POST['title']);
            $page->setSlug($_POST['slug']);
            $page->setBody($_POST['body']);
            if (isset($_POST['homepage'])){
                $page->setHomepage('true');
            }
            // @todo make sure we clear the template when it is set to null from something else
            if ($_POST['template'] !== 'null'){
                $page->setTemplate($_POST['template']);
            }
            if ($_POST['layout'] !== 'null'){
                $page->setLayout($_POST['layout']);
            }
            $storage = Zend_Registry::get('storage');;
            $storage->persist($page);

            $this->_redirect('/admin/pages');
        }

        $this->view->page = new Page();
        $this->view->action = 'add';

        $this->view->availableTemplates = $templates;
        $this->view->availableLayouts = $layouts;

        $this->renderScript('/admin/page/form.phtml');
    }

    /**
     * Edits a page
     */
    public function editAction()
    {
        $application = Zend_Registry::get('config')->application->toArray();
        // Get template and layout paths
        $templatePaths = Zend_Registry::get('config')->paths->templates->toArray();
        $layoutPaths = Zend_Registry::get('config')->paths->layouts->toArray();

        // Unset the application template and layout paths, these are not accessible for users.
        unset($templatePaths['fizzy'], $layoutPaths['fizzy']);

        // Get available templates and layouts
        $templates = $this->_getViewScripts($templatePaths);
        $layouts = $this->_getViewScripts($layoutPaths);

        // Unset default template and layout
        unset($templates[$application['defaultTemplate']], $layouts[$application['defaultLayout']]);

        $storage = Zend_Registry::get('storage');
        $page = $storage->fetchByID('Page', $this->_getParam('id'));
        
        if($this->_request->isPost()) {
            $page->setTitle(strip_tags($_POST['title']));
            $page->setSlug(strip_tags($_POST['slug']));
            $page->setBody($_POST['body']);

            if (isset($_POST['homepage'])){
                $page->setHomepage('true');
            } else {
                $page->setHomepage(null);
            }

            if ($_POST['template'] !== 'null'){
                $page->setTemplate($_POST['template']);
            } else {
                $page->setTemplate(null);
            }
            
            if ($_POST['layout'] !== 'null'){
                $page->setLayout($_POST['layout']);
            } else {
                $page->setLayout(null);
            }
            
            $storage->persist($page);

            $this->_redirect('/admin/pages');
        }

        $this->view->page = $page;
        $this->view->action = 'edit/' . $page->getId();

        $this->view->availableTemplates = $templates;
        $this->view->availableLayouts = $layouts;

        $this->renderScript('admin/page/form.phtml');
    }

    /**
     * Shows a delete confirmation and deletes if Yes is pressed
     */
    public function deleteAction()
    {
        $id = $this->_getParam('id');
        if(null !== $id)
        {
            $storage = Zend_Registry::get('storage');
            $page = $storage->fetchByID('Page', $id);
            $storage->delete($page);
        }
        
        $this->_redirect('/admin/pages');
    }

    /**
     * Returns the view scripts found in a directory.
     * @param string $path
     * @return array
     */
    protected function _getViewScripts($path)
    {
        if(!is_array($path)) {
            $path = array($path);
        }

        $scripts = array();
        foreach($path as $subPath) {
            $iterator = new DirectoryIterator($subPath);
            foreach($iterator as $file) {
                if(!$file->isFile()) { continue; }

                $extension = substr(
                                $file->getFilename(),
                                strrpos($file->getFilename(), '.') + 1,
                                strlen($file->getFilename()
                             ));
                if('phtml' !== $extension) { continue; }
                $script = str_replace(".{$extension}", '', $file->getFilename());
                $scripts[$script] = $script;
            }
        }

        return $scripts;
    }
}
