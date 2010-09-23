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
                    array(ROOT_PATH, 'library', 'Fizzy', 'View', 'Helper')
            )), 'Fizzy_View_Helper');
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

        $options = $this->getOptions();
        $backendSwitch = isset($options['backendSwitch']) ? strtolower((string) $options['backendSwitch']) : 'fizzy';

        $router = $this->getContainer()->frontcontroller->getRouter();
        $config = new Zend_Config_Ini(ROOT_PATH . '/configs/routes.ini');

        $routes = $config->{$this->getEnvironment()}->toArray();

        // Parse all routes and replace the backend switch
        foreach ($routes as &$route) {
            if (false !== strpos($route['route'], '{backend}')) {
                $route['route'] = str_replace('{backend}', $backendSwitch, $route['route']);
            }
        }

        // Load parsed routes into the router
        $router->addConfig(new Zend_Config($routes));
        return $router;
    }

    protected function _initTypes()
    {
        $config = new ZendL_Config_Yaml(ROOT_PATH . '/configs/types.yml');
        $types = Fizzy_Types::initialize($config);
        return $types;
    }

    /**
     * @todo make adapter class configurable
     */
    protected function _initSpam()
    {
        $this->bootstrap('Config');
        $config = $this->getContainer()->config;
        $adapter = new Fizzy_Spam_Adapter_Akismet($config->spam->akismetKey, $config->spam->siteUrl);
        Fizzy_Spam::setDefaultAdapter($adapter);
    }

    protected function _initModuleErrorHandler()
    {
        $this->bootstrap('FrontController');

        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Fizzy_Controller_Plugin_ErrorHandlerModuleSelector());
    }

    protected function _initTranslate()
    {
        $translate = new Zend_Translate(
        array(
            'adapter' => 'array',
            'content' => ROOT_PATH . '/languages/admin_nl.php',
            'locale' => 'nl'
            )
        );

        $session = new Zend_Session_Namespace('Lang');
        if (isset($session->language)){
            $translate->setLocale($session->language);
        } else {
            $session->language = $translate->getLocale();
        }

        Zend_Registry::set('Zend_Translate', $translate);
        return $translate;
    }

}
