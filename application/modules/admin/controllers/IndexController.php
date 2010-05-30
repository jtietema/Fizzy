<?php
/**
 * Class Admin_IndexController
 * @category Fizzy
 * @package Admin
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

        // Blog
        $items[] = new Fizzy_Navigation_Page_Route(array(
            'label' => 'Blogs',
            'route' => 'admin_blogs',
            'pages' => array(
                new Fizzy_Navigation_Page_Route(array(
                    'label' => 'Blog',
                    'route' => 'admin_blog',
                    'pages' => array(
                        new Fizzy_Navigation_Page_Route(array(
                            'label' => 'Edit Post',
                            'route' => 'admin_blog_post_edit',
                        )),
                        new Fizzy_Navigation_Page_Route(array(
                            'label' => 'Add Post',
                            'route' => 'admin_blog_post_add',
                        ))
                    )
                ))
            )
        ));

        // Comments
        $items[] = new Fizzy_Navigation_Page_Route(array(
            'label' => 'Comments',
            'route' => 'admin_comments',
            'pages' => array(
                new Fizzy_Navigation_Page_Route(array(
                    'label' => 'Thread list',
                    'route' => 'admin_comments_list',
                    'pages' => array(
                        new Fizzy_Navigation_Page_Route(array(
                            'label' => 'Show thread',
                            'route' => 'admin_comments_topic',
                            'pages' => array()
                        ))
                    )
                )),
                new Fizzy_Navigation_Page_Route(array(
                    'label' => 'Spambox',
                    'route' => 'admin_comments_spambox',
                    'pages' => array()
                )),
                new Fizzy_Navigation_Page_Route(array(
                    'label' => 'Edit comment',
                    'route' => 'admin_comments_edit',
                ))
            )
        ));

        // Pages
        $items[] = new Fizzy_Navigation_Page_Route(array(
            'label' => 'Pages',
            'route' => 'admin_pages',
            'pages' => array(
                new Fizzy_Navigation_Page_Route(array(
                    'label' => 'Add',
                    'route' => 'admin_pages_add',
                )),
                new Fizzy_Navigation_Page_Route(array(
                    'label' => 'Edit',
                    'route' => 'admin_pages_edit',
                )),
            )
        ));

        // Media
        $items[] = new Fizzy_Navigation_Page_Route(array(
            'label' => 'Media',
            'route' => 'admin_media',
            'pages' => array ()
        ));

        // Contact
        if (null !== Setting::getKey('log', 'contact') && 0 < Setting::getKey('log', 'contact')->value) {
            $items[] = new Zend_Navigation_Page_Mvc(array(
                'label' => 'Contact',
                'route' => 'admin_contact',
                'module' => 'admin',
                'controller' => 'contact',
                'action' => 'index',
                'pages' => array (
                    new Zend_Navigation_Page_Mvc(array(
                        'label' => 'Contact show',
                        'route' => 'admin_contact_show',
                        'module' => 'admin',
                        'controller' => 'contact',
                        'action' => 'show',
                    ))
                )
            ));
        }

        // Users
        $items[] = new Fizzy_Navigation_Page_Route(array(
            'label' => 'Users',
            'route' => 'admin_users',
            'pages' => array (
                new Fizzy_Navigation_Page_Route(array(
                    'label' => 'Add',
                    'route' => 'admin_users_add',
                )),
                new Fizzy_Navigation_Page_Route(array(
                    'label' => 'Edit',
                    'route' => 'admin_users_edit',
                )),
            )
        ));

        // Settings
        $items[] = new Fizzy_Navigation_Page_Route(array(
            'label' => 'Settings',
            'route' => 'admin_settings',
        ));

        // Logout
        $items[] = new Fizzy_Navigation_Page_Route(array(
            'label' => 'Logout',
            'route' => 'admin_logout',
        ));

        $this->view->items = $items;
        $this->renderScript('navigation.phtml');
    }
    
}
