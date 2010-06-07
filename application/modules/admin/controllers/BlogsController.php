<?php
/**
 * Class Admin_BlogsController
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

class Admin_BlogsController extends Fizzy_SecuredController
{
    
    public function indexAction()
    {
        $blogs = Doctrine_Query::create()->from('Blog')->execute();
        $this->view->blogs = $blogs;
    }

    public function blogAction()
    {
        $id = $this->_getParam('id', null);
        $pageNumber = $this->_getParam('page', 1);

        if (null === $id){
            return $this->renderScript('blogs/blogNotFound.phtml');
        }

        $blog = Doctrine_Query::create()->from('Blog')->where('id = ?', $id)->fetchOne();

        if (null == $blog){
            return $this->renderScript('blogs/blogNotFound.phtml');
        }

        $query = Doctrine_Query::create()->from('Post')
                ->where('blog_id = ?', $id);

        $paginator = new Zend_Paginator(new Fizzy_Paginator_Adapter_DoctrineQuery($query));
        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($pageNumber);

        $this->view->blog = $blog;
        $this->view->paginator = $paginator;
    }

    public function addPostAction()
    {
        $blogId = $this->_getParam('blog_id', null);
        if (null === $blogId){
            return $this->renderScript('blogs/blogNotFound.phtml');
        }

        $blog = Doctrine_Query::create()->from('Blog')->where('id = ?', $blogId)->fetchOne();
        if (null == $blog){
            return $this->renderScript('blogs/blogNotFound.phtml');
        }

        $this->view->blog = $blog;
        $this->view->form = $form = $this->_getPostForm();

        $form->date->setValue(date('Y-m-d H:i:s'));
        $auth = Zend_Auth::getInstance();
        $identity = $auth->getIdentity();
        $form->author->setValue($identity['id']);

        $form->addElement(new Zend_Form_Element_Submit('submit', array(
            'label' => 'Add',
            'ignore' => true
        )));

        $this->view->post = $post = new Post();

        if ($this->_request->isPost() && $form->isValid($_POST)){
            $post->title = $form->title->getValue();
            $post->body = $form->body->getValue();
            $post->author = $form->author->getValue();
            $post->date = $form->date->getValue();
            $post->blog_id = $blogId;
            $post->save();

            $this->_redirect('@admin_blog', array('id' => $blogId));
        }
        
        $this->renderScript('blogs/post-form.phtml');
    }

    public function editPostAction()
    {
        $postId = $this->_getParam('post_id', null);
        if (null === $postId){
            return $this->renderScript('blogs/postNotFound.phtml');
        }

        $post = Doctrine_Query::create()->from('Post')->where('id = ?', $postId)->fetchOne();

        if (null == $post){
            return $this->renderScript('blogs/postNotFound.phtml');
        }

        $form = $this->_getPostForm();

        if ($this->_request->isPost() && $form->isValid($_POST)){
            $post->title = $form->title->getValue();
            $post->body = $form->body->getValue();
            $post->author = $form->author->getValue();
            $post->date = $form->date->getValue();
            $post->comments = $form->comments->getValue();
            $post->status = $form->status->getValue();
            $post->save();
            $this->addSuccessMessage("Post \"<strong>{$post->title}</strong>\" was successfully saved.");
            $this->_redirect('@admin_post_edit', array('id' => $postId));
        } else {
            $form->title->setValue($post->title);
            $form->body->setValue($post->body);
            $form->author->setValue($post->author);
            $form->date->setValue($post->date);
            $form->comments->setValue($post->comments);
            $form->status->setValue($post->status);
        }
        
        $form->addElement(new Zend_Form_Element_Submit('submit', array(
            'label' => 'Edit',
            'ignore' => true,
        )));

        $this->view->form = $form;
        $this->view->post = $post;
        $this->renderScript('blogs/post-form.phtml');
    }

    public function deletePostAction()
    {
        $postId = $this->_getParam('post_id', null);
        if (null === $postId){
            return $this->renderScript('blogs/postNotFound.phtml');
        }

        $post = Doctrine_Query::create()->from('Post')->where('id = ?', $postId)->fetchOne();

        if (null == $post){
            return $this->renderScript('blogs/postNotFound.phtml');
        }

        $post->delete();

        $this->_redirect('@admin_blog', array('id' => $post->Blog->id));
    }

    protected function _getPostForm()
    {
        $form = new Zend_Form();
        $form->addElement(new Zend_Form_Element_Text('title', array(
            'label' => 'Title',
            'required' => true,
        )));

        $form->addElement(new ZendX_JQuery_Form_Element_DatePicker('date', array(
            'label' => 'Date',
            'required' => true,
            'description' => 'The published date of this post.'
        )));
        $form->date->setJQueryParam('changeMonth', true);
        $form->date->setJQueryParam('changeYear', true);
        $form->date->setJQueryParam('dateFormat', 'yy-mm-dd');

        $form->addElement(new Fizzy_Form_Element_Wysiwyg('body', array(
            'label' => 'Body',
            'attribs' => array('style' => 'width: 100%;'),
        )));

        $form->addElement(new Zend_Form_Element_Select('author', array(
            'label' => 'Author',
            'multiOptions' => $this->_getUsers(),
            'description' => 'The author of this post.'
        )));

        $form->addElement(new Zend_Form_Element_Checkbox('comments', array(
            'label' => 'Allow comments',
            'value' => true,
            'description' => 'Are visitors allowed to comment on this post?'
        )));

        $form->addElement(new Zend_Form_Element_Select('status', array(
            'label' => 'Status',
            'multiOptions' => array(
                0 => 'Draft',
                1 => 'Pending review',
                2 => 'Published'
            ),
            'description' => 'Only published posts will show up on the website.'
        )));

        return $form;
    }

    protected function _getUsers()
    {
        $users = Doctrine_Query::create()->from('User')->execute();

        $array = array();
        foreach ($users as $user){
            $array[$user->id] = $user->displayname;
        }
        
        return $array;
    }
}
