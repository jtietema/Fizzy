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

    public function rssAction()
    {
        $feed = $this->_feed();
        $feed->setFeedLink('http://www.example.com/blog/rss', 'rss');

        $this->view->rss = $feed->export('rss');
    }

    public function atomAction()
    {
        $feed = $this->_feed();
        $feed->setFeedLink('http://www.example.com/blog/atom', 'atom');

        $this->view->atom = $feed->export('atom');
        
    }

    protected function _feed()
    {
        $this->_helper->layout->disableLayout();
        $this->_response->setHeader('Content-Type', 'text/xml');

        $feed = new Zend_Feed_Writer_Feed();
        $feed->setTitle('Fizzy Example Blog');
        $feed->setLink('http://www.example.com');
        $feed->setDescription('Example of a Rss feed from the Fizzy Blog module');
        
        $feed->addAuthor(array(
            'name'  => 'Paddy',
            'email' => 'paddy@example.com',
            'uri'   => 'http://www.example.com',
        ));
        $feed->setDateModified(time());

        $posts = Doctrine_Query::create()
                    ->from('Post')
                    ->where('status = ?', Post::PUBLISHED)
                    ->orderBy('date DESC')
                    ->limit(10)
                    ->execute();

        foreach ($posts as $post){
            $entry = $feed->createEntry();
            $entry->setTitle($post->title);
            $entry->setLink('http://www.example.com/all-your-base-are-belong-to-us');
            $entry->addAuthor(array(
                'name'  => $post->User->displayname,
            ));
            $entry->setDateModified(new Zend_Date($post->date));
            $entry->setDateCreated(new Zend_Date($post->date));
            $entry->setContent($post->body);
            $feed->addEntry($entry);
        }

        return $feed;
    }
    
}