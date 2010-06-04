<?php

class Fizzy_View_Helper_CommentStream extends Zend_View_Helper_Abstract
{

    public function commentStream($streamId, $template, $options = array())
    {
        // Update options with defaults
        $defaults = array (
            'gravatar_enabled' => true,
            'gravatar_size' => '50',
        );
        $options = $options + $defaults;

        // Get the comments for the stream
        $comments = Doctrine_Query::create()
                        ->from('Comments')
                        ->where('post_id = ?', $streamId)
                        ->limit(10)
                        ->execute();

        if (false === $comments) {
            return '<p>Could not load comment stream.</p>';
        }

        if (null === $this->view) {
            $this->view = new Zend_View();
        }

        return $this->view->partial($template, array('comments' => $comments, 'options' => $options));
    }
    
}