<?php

/**
 * Description of Pages
 *
 * @author mattijs
 */
class PagesController extends Fizzy_Controller
{

    public function slugAction()
    {
        $slug = $this->_getParam('slug', null);
        if(null === $slug) {
            # Redirect to 404
        }

        $storage = Zend_Registry::get('storage');
        $pages = $storage->fetchByField('Page', array('slug' => $slug));
        if(1 < count($pages)) {
            # Log user exception viewable in the backend
        }

        $page = array_shift($pages);

        $config = Zend_Registry::get('config');
        $templatePaths = $config->paths->templates->toArray();
        $this->view->setScriptPath($templatePaths);
        $this->_helper->layout->setLayout($page->getLayout());

        $this->view->page = $page;
        $this->renderScript($page->getTemplate());
    }
}