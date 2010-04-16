<?php

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
