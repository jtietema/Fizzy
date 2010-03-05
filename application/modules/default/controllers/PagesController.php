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
            # Get default homepage
            $query = Doctrine_Query::create()->from('Page')->where('homepage = ?', true);
            $page = $query->fetchOne();
        } else {
            $query = Doctrine_Query::create()->from('Page')->where('slug = ?', $slug);
            $page = $query->fetchOne();
        }

        if(!$page) {
            $this->renderScript('error/404.phtml');
            return;
        }

        $config = Zend_Registry::get('config');
        $templatePath = $config->paths->templatePath;
        $this->view->setScriptPath($templatePath);
        $this->_helper->layout->setLayout($page->getLayout());

        $this->view->page = $page;
        $this->renderScript($page->getTemplate());
    }
}
