<?php
/**
 * Class PagesController
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

        $this->_showPage($page);
    }

    /**
     * Shows a page by slug.
     */
    public function showAction()
    {
        $slug = $this->_getParam('slug');
        if(empty($slug)) {
            $page = $this->_storage->fetchColumn('page', 'homepage', 'true');
        } else {
            $page = $this->_storage->fetchColumn('page', 'slug', $slug);
        }

        $this->_showPage($page);
    }

    /**
     * Prepares the view to render a page.
     * @param Page|null $page
     */
    protected function _showPage($page)
    {
        if(null !== $page) {
            $paths = Fizzy_Config::getInstance()->getConfiguration('paths');
            $templates = Fizzy_Config::getInstance()->getPath('templates');

            $this->getView()->setScriptPaths($templates);
            $this->getView()->setScript($page->getTemplate() . '.phtml');
            $this->getView()->setLayout($page->getLayout());

            $this->getView()->page = $page;
        }
        else {
            $application = Fizzy_Config::getInstance()->getSection(Fizzy_Config::SECTION_APPLICATION);
            $this->getView()->setLayout($application['defaultLayout']);
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