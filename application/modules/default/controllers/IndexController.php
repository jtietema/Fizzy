<?php

class IndexController extends Zend_Controller_Action
{
    
    public function indexAction()
    {
        $blog = Doctrine_Query::create()->from('Blog')->where('slug = ?', 'default')->fetchOne();
        $this->view->posts = $blog->publishedPosts();
    }
}
