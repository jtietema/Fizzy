<?php
/**
 * Class Admin_IndexController
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */

class Admin_IndexController extends Fizzy_SecuredController
{

    protected $_sessionNamespace = 'fizzy';
    protected $_redirect = '/fizzy/login';

    /**
     * Default action redirects to Pages overview.
     */
    public function indexAction()
    {
        $this->_redirect('/fizzy/pages');
    }

    public function configurationAction()
    {
        $this->view->config = Zend_Registry::get('config')->toArray();
        $this->renderScript('configuration.phtml');
    }

    /**
     * Renders navigation for the admin section.
     */
    public function navigationAction() {
        $items = array();

        $items[] = new Zend_Navigation_Page_Mvc(array(
            'label' => 'Blogs',
            'route' => 'admin_blogs',
            'module' => 'admin',
            'controller' => 'blogs',
            'action' => 'index',
            'pages' => array(
                new Zend_Navigation_Page_Mvc(array(
                    'label' => 'Blog',
                    'route' => 'admin_blog',
                    'module' => 'admin',
                    'controller' => 'blogs',
                    'action' => 'blog',
                    'pages' => array(
                        new Zend_Navigation_Page_Mvc(array(
                            'label' => 'Edit Post',
                            'route' => 'admin_blog_post_edit',
                            'module' => 'admin',
                            'controller' => 'blogs',
                            'action' => 'edit-post'
                        )),
                        new Zend_Navigation_Page_Mvc(array(
                            'label' => 'Add Post',
                            'route' => 'admin_blog_post_add',
                            'module' => 'admin',
                            'controller' => 'blogs',
                            'action' => 'add-post'
                        ))
                    )
                ))
            )
        ));

        $items[] = new Zend_Navigation_Page_Mvc(array(
            'label' => 'Pages',
            'route' => 'admin_pages',
            'module' => 'admin',
            'controller' => 'pages',
            'action' => 'index',
            'pages' => array(
                new Zend_Navigation_Page_Mvc(array(
                    'label' => 'Add',
                    'route' => 'admin_pages_add',
                    'module' => 'admin',
                    'controller' => 'pages',
                    'action' => 'add',
                )),
                new Zend_Navigation_Page_Mvc(array(
                    'label' => 'Edit',
                    'route' => 'admin_pages_edit',
                    'module' => 'admin',
                    'controller' => 'pages',
                    'action' => 'edit',
                )),
            )
        ));

        $items[] = new Zend_Navigation_Page_Mvc(array(
            'label' => 'Media',
            'route' => 'admin_media',
            'module' => 'admin',
            'controller' => 'media',
            'action' => 'index',
            'pages' => array ()
        ));

        $items[] = new Zend_Navigation_Page_Mvc(array(
            'label' => 'Users',
            'route' => 'admin_users',
            'module' => 'admin',
            'controller' => 'user',
            'action' => 'index',
            'pages' => array (
                new Zend_Navigation_Page_Mvc(array(
                    'label' => 'Add',
                    'route' => 'admin_users_add',
                    'module' => 'admin',
                    'controller' => 'user',
                    'action' => 'add',
                )),
                new Zend_Navigation_Page_Mvc(array(
                    'label' => 'Edit',
                    'route' => 'admin_users_edit',
                    'module' => 'admin',
                    'controller' => 'user',
                    'action' => 'edit',
                )),
            )
        ));

        $items[] = new Zend_Navigation_Page_Mvc(array(
            'label' => 'Logout',
            'route' => 'admin_logout',
            'module' => 'admin',
            'controller' => 'auth',
            'action' => 'logout'
        ));

        $this->view->items = $items;
        $this->renderScript('navigation.phtml');
    }
    
}
