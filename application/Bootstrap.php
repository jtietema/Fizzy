<?php
/**
 * Class Bootstrap
 * @category Fizzy
 * @package Fizzy_Bootstrap
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

/**
 * Description of Bootstrap
 *
 * @author jeroen
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Initializes a new view object and loads the view directories into it.
     * @return Zend_View
     */
    protected function _initView()
    {
        $view = new Zend_View();

        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
        $viewRenderer->setView($view);
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);
        $view->addHelperPath(
            realpath(implode(DIRECTORY_SEPARATOR,
                    array(ROOT_PATH, 'library', 'ZendX', 'JQuery', 'View', 'Helper')
            )), 'ZendX_JQuery_View_Helper');

        return $view;
    }


    /**
     * Starts Zend_Layout in MVC mode.
     * @todo add custom front controller plugin for layout allow multiple layouts
     * @return Zend_Layout
     */
    protected function _initLayout()
    {
        $this->bootstrap('FrontController');

        $layout = Zend_Layout::startMvc(array (
            'layout' => 'default',
        ));

        $front = $this->getResource('FrontController');
        $front->registerPlugin(new Fizzy_Layout_ControllerPlugin());

        return $layout;
    }

    protected function _initConfig()
    {
        $config = new Zend_Config($this->getOptions());

        Zend_Registry::set('config', $config);

        return $config;
    }

    protected function _initRoutes()
    {
        $this->bootstrap('FrontController');
        $router = $this->getContainer()->frontcontroller->getRouter();
        $config = new Zend_Config_Ini(ROOT_PATH . '/configs/routes.ini');
        $router->addConfig($config->{$this->getEnvironment()});
        return $router;
    }

}
