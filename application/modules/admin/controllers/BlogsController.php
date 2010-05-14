<?php

class Admin_BlogsController extends Fizzy_SecuredController
{
    protected $_sessionNamespace = 'fizzy';
    protected $_redirect = '/fizzy/login';
    
    public function indexAction()
    {
        $blogs = Doctrine_Query::create()->from('Blog')->execute();
        $this->view->blogs = $blogs;
    }

    public function blogAction()
    {
        $id = $this->_getParam('id', null);
        if (null === $id){
            return $this->renderScript('blogs/blogNotFound.phtml');
        }

        $blog = Doctrine_Query::create()->from('Blog')->where('id = ?', $id)->fetchOne();

        if (null == $blog){
            return $this->renderScript('blogs/blogNotFound.phtml');
        }

        $this->view->blog = $blog;
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

            $this->_redirect('/fizzy/blog/' . $blogId);
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
            $post->save();
        } else {
            $form->title->setValue($post->title);
            $form->body->setValue($post->body);
            $form->author->setValue($post->author);
            $form->date->setValue($post->date);
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

        $this->_redirect('fizzy/blog/' . $post->Blog->id);
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
        )));
        $form->date->setJQueryParam('changeMonth', true);
        $form->date->setJQueryParam('changeYear', true);

        $form->addElement(new Fizzy_Form_Element_Wysiwyg('body', array(
            'label' => 'Body',
        )));

        $form->addElement(new Zend_Form_Element_Select('author', array(
            'label' => 'Author',
            'multiOptions' => $this->_getUsers()
        )));

        return $form;
    }

    protected function _getUsers()
    {
        $users = Doctrine_Query::create()->from('User')->execute();

        $array = array();
        foreach ($users as $user){
            $array[$user->id] = $user->username;
        }
        
        return $array;
    }
}
