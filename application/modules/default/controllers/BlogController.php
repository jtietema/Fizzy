<?php
/**
 * Class BlogController
 * @package Fizzy
 * @subpackage Controllers
 * @category frontend
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
 * @copyright 2010 Voidwalkers (http://www.voidwalkers.nl)
 * @license http://www.voidwalkers.nl/license/new-bsd The New BSD License
 */

/**
 * Default blog implementation
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class BlogController extends Fizzy_Controller
{

    /**
     * Shows latest posts from all blogs
     */
    public function indexAction()
    {
        $posts = Doctrine_Query::create()
                    ->from('Post')
                    ->where('status = ?', Post::PUBLISHED)
                    ->orderBy('date DESC')
                    ->limit(10)
                    ->execute();
        
        $this->view->posts = $posts;
        $this->render('posts');
    }

    /**
     * Shows the posts from a single blog
     */
    public function blogAction()
    {
        $blogSlug = $this->_getParam('blog_slug', null);
        if (null === $blogSlug) {
            // show 404
        }

        $blog = Doctrine_Query::create()->from('Blog')->where('slug = ?', $blogSlug)->fetchOne();
        if (false === $blog) {
            // Show 404
        }

        $posts = Doctrine_Query::create()
                    ->from('Post')
                    ->where('status = ?', Post::PUBLISHED)
                    ->where('blog_id = ?', $blog->id)
                    ->orderBy('date DESC')
                    ->limit(10)
                    ->execute();
        
        $this->view->blog = $blog;
        $this->view->posts = $posts;
        $this->render('blog');
    }

    /**
     * Shows all posts by a user
     */
    public function userAction()
    {
        $username = $this->_getParam('username', null);
        if (null === $username) {
            // Show 404
        }

        $user = Doctrine_Query::create()
                    ->from('User')
                    ->where('username = ?', $username)
                    ->fetchOne();
        if (false === $user) {
            // Show 404
        }

        $posts = Doctrine_Query::create()
                    ->from('Post')
                    ->where('status = ?', Post::PUBLISHED)
                    ->where('author = ?', $user->id)
                    ->orderBy('date DESC')
                    ->limit(10)
                    ->execute();

        $this->view->user = $user;
        $this->view->posts = $posts;
        $this->render('user');
    }

    /**
     * Shows a single post
     */
    public function postAction()
    {
        $slug = $this->_getParam('post_slug', null);

        if (null === $slug) {
            // Show 404
        }

        $post = Doctrine_Query::create()->from('Post')->where('slug = ?', $slug)->fetchOne();
        if (false === $post) {
            // Show 404
        }

        $this->view->post = $post;
        $this->render('post-entry');
    }
    
}