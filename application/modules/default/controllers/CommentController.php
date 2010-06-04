<?php

class CommentController extends Fizzy_Controller
{

    public function addAction()
    {
        if (!$this->getRequest()->isPost()) {
            // Deny
        }

        $stream = $this->_getParam('stream', null);
        if (null === $stream) {
            // Deny
        }

        $comment = new Comments();
        $comment->post_id = $stream;
        $comment->name = $_POST['name'];
        $comment->email = $_POST['email'];
        $comment->website = $_POST['website'];
        $comment->body = $_POST['body'];
        $comment->date = time();
        $comment->save();

        if (isset($_SERVER['HTTP_REFERER'])) {
            $this->_redirect($_SERVER['HTTP_REFERER']);
        }
        else {
            $this->render('success.phtml');
        }
    }
}