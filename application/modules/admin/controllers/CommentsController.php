<?php
/**
 * Class Admin_CommentsController
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
 * Controller class for the moderation panel of comments
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
class Admin_CommentsController extends Fizzy_SecuredController
{
    protected $_sessionNamespace = 'fizzy';
    protected $_redirect = '/fizzy/login';

    /**
     * Dashboard. Shows latest comments and total numbers of comments, spam, etc.
     */
    public function indexAction()
    {
        $pageNumber = $this->_getParam('page', 1);
        
        $query = Doctrine_Query::create()->from('Comments')->where('spam = 0')->orderBy('id DESC');

        $this->view->totalComments = $query->count();

        $paginator = new Zend_Paginator(new Fizzy_Paginator_Adapter_DoctrineQuery($query));
        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($pageNumber);

        $spamQuery = Doctrine_Query::create()->from('Comments')->where('spam = 1');
        $this->view->spamComments = $spamQuery->count();
        $this->view->paginator = $paginator;
    }

    /**
     * List of discussions/topics
     */
    public function listAction()
    {
        $pageNumber = $this->_getParam('page', 1);
        
        $query = Doctrine_Query::create()->from('Comments')
                ->groupBy('post_id')->orderBy('id DESC');

        $paginator = new Zend_Paginator(new Fizzy_Paginator_Adapter_DoctrineQuery($query));
        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($pageNumber);
        
        $this->view->paginator = $paginator;
    }

    /**
     * Shows one discussion/topic/thread.
     */
    public function topicAction()
    {
        $id = $this->_getParam('id', null);
        $pageNumber = $this->_getParam('page', 1);

        if (null === $id){
            return $this->renderScript('comments/topic-not-found.phtml');
        }

        $query = Doctrine_Query::create()->from('Comments')
                ->where('post_id = ?', $id)->andWhere('spam = 0')->orderBy('id DESC');

        $paginator = new Zend_Paginator(new Fizzy_Paginator_Adapter_DoctrineQuery($query));
        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($pageNumber);

        $tempModel = $query->fetchOne();
        $this->view->threadModel = $tempModel->getThreadModel();
        $this->view->paginator = $paginator;
    }

    /**
     * Marks given message as spam.
     */
    public function spamAction()
    {
        $id = $this->_getParam('id', null);

        if (null === $id){
            return $this->renderScript('comments/comment-not-found.phtml');
        }

        $query = Doctrine_Query::create()->from('Comments')
                ->where('id = ?', $id);

        $comment = $query->fetchOne();

        if (null == $comment){
            return $this->renderScript('comments/comment-not-found.phtml');
        }

        $comment->spam = true;
        $comment->save();
        /**
         * @todo pass to the spam backend
         */

        $this->_redirectBack($comment);
    }

    /**
     * Unmarks given message as spam.
     */
    public function hamAction()
    {
        $id = $this->_getParam('id', null);

        if (null === $id){
            return $this->renderScript('comments/comment-not-found.phtml');
        }

        $query = Doctrine_Query::create()->from('Comments')
                ->where('id = ?', $id);

        $comment = $query->fetchOne();

        if (null == $comment){
            return $this->renderScript('comments/comment-not-found.phtml');
        }

        $comment->spam = false;
        $comment->save();
        /**
         * @todo pass to the Spam backend
         */

        $this->_redirectBack($comment);
    }

    /**
     * Edit the comment.
     */
    public function editAction()
    {
        $id = $this->_getParam('id', null);
        $redirect = $this->_getParam('back', 'dashboard');

        if (null === $id){
            return $this->renderScript('comments/comment-not-found.phtml');
        }

        $query = Doctrine_Query::create()->from('Comments')
                ->where('id = ?', $id);

        $comment = $query->fetchOne();

        if (null == $comment){
            return $this->renderScript('comments/comment-not-found.phtml');
        }

        $form = new Zend_Form();
        $form->setAction($this->view->baseUrl('/fizzy/comment/edit/' . $comment->id . '?back=' . $redirect));
        
        $form->addElement(new Zend_Form_Element_Text('name', array(
            'label' => 'Author name'
        )));
        $form->addElement(new Zend_Form_Element_Text('email', array(
            'label' => 'Author E-mail'
        )));
        $form->addElement(new Zend_Form_Element_Text('website', array(
            'label' => 'Author website'
        )));
        $form->addElement(new Zend_Form_Element_Textarea('body', array(
            'label' => 'Comment'
        )));
        $form->addElement(new Zend_Form_Element_Submit('save', array(
            'label' => 'Save'
        )));

        if ($this->_request->isPost() && $form->isValid($_POST)){
            $comment->name = $form->name->getValue();
            $comment->email = $form->email->getValue();
            $comment->website = $form->website->getValue();
            $comment->body = $form->body->getValue();
            $comment->save();

            $this->_redirectBack($comment);
        }

        $form->name->setValue($comment->name);
        $form->email->setValue($comment->email);
        $form->website->setValue($comment->website);
        $form->body->setValue($comment->body);

        $this->view->form = $form;
        $this->view->comment = $comment;
        $this->view->back = $redirect;
    }

    /**
     * Delete the comment.
     */
    public function deleteAction()
    {
        $id = $this->_getParam('id', null);
        
        if (null === $id){
            return $this->renderScript('comments/comment-not-found.phtml');
        }

        $query = Doctrine_Query::create()->from('Comments')
                ->where('id = ?', $id);

        $comment = $query->fetchOne();

        if (null == $comment){
            return $this->renderScript('comments/comment-not-found.phtml');
        }

        $comment->delete();

        $this->_redirectBack($comment);
    }

    /**
     * Approve the comment (when using moderation).
     */
    public function approveAction()
    {

    }

    /**
     * Unapprove the given comment.
     */
    public function unapproveAction()
    {

    }

    /**
     * Shows the spambox containing all spam comments
     */
    public function spamboxAction()
    {
        $pageNumber = $this->_getParam('page', 1);
        
        $query = Doctrine_Query::create()->from('Comments')->where('spam = ?', 1);

        $paginator = new Zend_Paginator(new Fizzy_Paginator_Adapter_DoctrineQuery($query));
        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($pageNumber);

        $this->view->paginator = $paginator;
    }

    protected function _redirectBack(Comments $comment = null)
    {
        $redirect = $this->_getParam('back', 'dashboard');

        switch($redirect){
            case 'topic':
                $this->_redirect('/fizzy/comments/topic/' . $comment->post_id);
            break;
            case 'spambox':
                $this->_redirect('/fizzy/comments/spam');
            break;
            case 'post':
                $postId = (int) substr($comment->post_id, 5);
                $this->_redirect('/fizzy/post/' . $postId . '/edit');
            default:
                $this->_redirect('/fizzy/comments');
            break;
        }
    }
}
