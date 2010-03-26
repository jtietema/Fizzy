<?php

/**
 * Description of Bootstrap
 *
 * @author jeroen
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected $_defaultConfig = array (
        'application' => array(
            'title' => 'Fizzy',
            'basePath' => '',
            'defaultTemplate' => 'page.phtml',
            'backendPrefix' => 'fizzy'
        ),
        'routes' => array (
            # Catch all for pages slugs
            'page_by_slug' => array (
                'route' => '/:slug',
                'defaults' => array (
                    'controller' => 'pages',
                    'action' => 'slug'
                )
            ),
            'contact' => array(
                'route' => '/contact',
                'defaults' => array(
                    'controller' => 'contact',
                    'action' => 'index'
                )
            ),
            # Admin pages control
            'admin_pages' => array(
                'type' => 'Zend_Controller_Router_Route_Static',
                'route' => '/fizzy/pages',
                'defaults' => array (
                    'controller' => 'pages',
                    'action' => 'index',
                    'module' => 'admin'
                )
            ),
            'admin_pages_add' => array(
                'route' => '/fizzy/pages/add',
                'defaults' => array (
                    'controller' => 'pages',
                    'action' => 'add',
                    'module' => 'admin'
                )
            ),
            'admin_pages_edit' => array(
                'route' => '/fizzy/pages/edit/:id',
                'defaults' => array (
                    'controller' => 'pages',
                    'action' => 'edit',
                    'module' => 'admin'
                )
            ),
            'admin_pages_delete' => array(
                'route' => '/fizzy/pages/delete/:id',
                'defaults' => array (
                    'controller' => 'pages',
                    'action' => 'delete',
                    'module' => 'admin'
                )
            ),
            # Admin media
            'admin_media_delete' => array(
                'route' => '/fizzy/media/delete/:name',
                'defaults' => array (
                    'controller' => 'media',
                    'action' => 'delete',
                    'module' => 'admin'
                )
            ),
            'admin_media' => array(
                'type' => 'Zend_Controller_Router_Route_Static',
                'route' => '/fizzy/media',
                'defaults' => array (
                    'controller' => 'media',
                    'module' => 'admin'
                )
            ),
            'admin_media_gallery' => array(
                'route' => '/fizzy/media/gallery',
                'defaults' => array(
                    'controller' => 'media',
                    'action' => 'gallery',
                    'module' => 'admin'
                )
            ),
            'fizzy_users' => array(
                'route' => '/fizzy/users',
                'defaults' => array (
                    'controller' => 'user',
                    'module' => 'admin'
                )
            ),
            'fizzy_user_add' => array(
                'type' => 'Zend_Controller_Router_Route_Static',
                'route' => '/fizzy/user/add',
                'defaults' => array (
                    'controller' => 'user',
                    'action' => 'add',
                    'module' => 'admin'
                )
            ),
            'fizzy_user_edit' => array(
                'route' => '/fizzy/user/edit/:id',
                'defaults' => array (
                    'controller' => 'user',
                    'action' => 'edit',
                    'module' => 'admin'
                )
            ),
            'fizzy_user_delete' => array(
                'route' => '/fizzy/user/delete/:id',
                'defaults' => array (
                    'controller' => 'user',
                    'action' => 'delete',
                    'module' => 'admin'
                )
            ),
            # Static admin routes
            'admin_configuration' => array(
                'type' => 'Zend_Controller_Router_Route_Static',
                'route' => '/fizzy/configuration',
                'defaults' => array (
                    'controller' => 'index',
                    'action' => 'configuration',
                    'module' => 'admin'
                )
            ),
            'fizzy_logout' => array(
                'type' => 'Zend_Controller_Router_Route_Static',
                'route' => '/fizzy/logout',
                'defaults' => array (
                    'controller' => 'auth',
                    'action' => 'logout',
                    'module' => 'admin'
                )
            ),
            'fizzy_login' => array(
                'type' => 'Zend_Controller_Router_Route_Static',
                'route' => '/fizzy/login',
                'defaults' => array (
                    'controller' => 'auth',
                    'action' => 'login',
                    'module' => 'admin'
                )
            ),
            'admin' => array (
                'type' => 'Zend_Controller_Router_Route_Static',
                'route' => '/fizzy',
                'defaults' => array (
                    'controller' => 'index',
                    'action' => 'index',
                    'module' => 'admin'
                )
            ),
        ),
        'paths' => array(
            'application' => 'application',
            'controllers' => array (
                'default' => 'application/modules/default/controllers',
                'admin' => 'application/modules/admin/controllers',
            ),
            'models' => 'application/models',
            'templatePath' => 'application/modules/default/views/templates',
            'layoutPath' => 'application/modules/default/views/layouts',
            'assets' => 'application/assets',
            'configs' => 'configs',
            'data' => 'data',
            'log' => 'data/fizzy.log',
            'library' => 'library',
            'public' => 'public',
        ),
    );

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

}
