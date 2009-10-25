<?php
/**
 * Class PagesController
 */

/** Fizzy_Controller */
require_once 'Fizzy/Controller.php';

/** Fizzy_Storage */
require_once 'Fizzy/Storage.php';

/**
 * Pages controller.
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class PageController extends Fizzy_Controller
{
    /**
     * Pages storage object.
     * @var Fizzy_Storage
     */
    protected $_storage = null;

    /** **/

    protected function _init()
    {
        $config = Fizzy_Config::getInstance();
        $this->_storage = new Fizzy_Storage($config->getSection('storage'));
    }

    /**
     * Default action, redirects to homepageAction.
     */
    public function defaultAction()
    {
        $this->_redirect('/');
    }

    /**
     * Shows the homepage.
     */
    public function homepageAction()
    {
        $page = $this->_storage->fetchColumn('page', 'homepage', 'true');
        $basePath = Fizzy_Config::getInstance()->getPath('base');
        $templates = Fizzy_Config::getInstance()->getPath('templates');

        $this->getView()->setScriptPaths($templates);
        $this->getView()->setScript($page->getTemplate() . '.phtml');

        $this->getView()->page = $page;
    }

    /**
     * Shows a page by slug.
     */
    public function showAction()
    {
        $slug = $this->_getParam('slug');
        $page = $this->_storage->fetchColumn('page', 'slug', $slug);

        if(null !== $page) {
            $paths = Fizzy_Config::getInstance()->getConfiguration('paths');
            $basePath = Fizzy_Config::getInstance()->getPath('base');
            $templates = Fizzy_Config::getInstance()->getPath('templates');

            $this->getView()->setScriptPaths($templates);
            $this->getView()->setScript($page->template . '.phtml');

            $this->getView()->page = $page;
        }
        else {
            $this->getView()->setScript('404.phtml');
        }
    }

    /**
     * Shows a list of pages.
     */
    public function listAction()
    {
        $pages = $this->_storage->fetchAll('page');
        $this->getView()->pages = $pages;
    }


}