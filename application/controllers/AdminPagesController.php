<?php

require_once 'SecureController.php';

class AdminPagesController extends SecureController
{

    /**
     * Shows a list of pages.
     */
    public function listAction()
    {
        $config = Fizzy_Config::getInstance();
        $storageOptions = $config->getSection('storage');
        $storage = new Fizzy_Storage($storageOptions);

        $pages = $storage->fetchAll('page');
        $this->getView()->pages = $pages;
        $this->getView()->setScript('admin/pages.phtml');
    }

    /**
     * Adds a page to the cms.
     */
    public function addAction()
    {
        $application = Fizzy_Config::getInstance()->getSection(Fizzy_Config::SECTION_APPLICATION);
        // Get template and layout paths
        $templatePaths = Fizzy_Config::getInstance()->getPath('templates');
        $layoutPaths = Fizzy_Config::getInstance()->getPath('layouts');

        // Unset the application template and layout paths, these are not accessible for users.
        unset($templatePaths['application'], $layoutPaths['application']);

        // Get available templates and layouts
        $templates = $this->_getViewScripts($templatePaths);
        $layouts = $this->_getViewScripts($layoutPaths);
        
        // Unset default template and layout
        unset($templates[$application['defaultTemplate']], $layouts[$application['defaultLayout']]);

        if($this->getRequest()->getMethod() === Fizzy_Request::METHOD_POST) {
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
            $config = Fizzy_Config::getInstance();
            $storageOptions = $config->getSection('storage');
            $storage = new Fizzy_Storage($storageOptions);

            $storage->persist($page);

            $this->_redirect('/admin/pages');
        }

        $this->getView()->page = new Page();
        $this->getView()->action = 'add';

        $this->getView()->availableTemplates = $templates;
        $this->getView()->availableLayouts = $layouts;

        $this->getView()->setScript('admin/page/form.phtml');
    }

    public function editAction()
    {
        $application = Fizzy_Config::getInstance()->getSection(Fizzy_Config::SECTION_APPLICATION);
        // Get template and layout paths
        $templatePaths = Fizzy_Config::getInstance()->getPath('templates');
        $layoutPaths = Fizzy_Config::getInstance()->getPath('layouts');

        // Unset the application template and layout paths, these are not accessible for users.
        unset($templatePaths['application'], $layoutPaths['application']);

        // Get available templates and layouts
        $templates = $this->_getViewScripts($templatePaths);
        $layouts = $this->_getViewScripts($layoutPaths);

        // Unset default template and layout
        unset($templates[$application['defaultTemplate']], $layouts[$application['defaultLayout']]);

        $config = Fizzy_Config::getInstance();
        $storageOptions = $config->getSection('storage');
        $storage = new Fizzy_Storage($storageOptions);

        if($this->getRequest()->getMethod() === Fizzy_Request::METHOD_POST) {
            $page = $storage->fetchOne('page', $this->_getParam('id'));
            $page->setTitle(strip_tags($_POST['title']));
            $page->setSlug(strip_tags($_POST['slug']));
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
            $storage->persist($page);

            $this->_redirect('/admin/pages');
        }

        $this->getView()->page = $page = $storage->fetchOne('page', $this->_getParam('id'));
        $this->getView()->action = 'edit/' . $page->getId();

        $this->getView()->availableTemplates = $templates;
        $this->getView()->availableLayouts = $layouts;

        $this->getView()->setScript('admin/page/form.phtml');
    }

    public function deleteAction()
    {
        $config = Fizzy_Config::getInstance();
        $storageOptions = $config->getSection('storage');
        $storage = new Fizzy_Storage($storageOptions);

        if($this->getRequest()->getMethod() === Fizzy_Request::METHOD_POST) {
            if ($_POST['confirm'] === 'Yes'){
                $page = $storage->fetchOne('page', $this->_getParam('id'));
                $storage->remove($page);
            }
            $this->_redirect('/admin/pages');
        }

        $this->getView()->page = $storage->fetchOne('page', $this->_getParam('id'));
        $this->getView()->setScript('admin/page/delete.phtml');
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
