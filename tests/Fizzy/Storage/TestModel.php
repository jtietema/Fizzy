<?php

require_once 'Fizzy/Storage/Model.php';

/**
 * Description of TestModel
 *
 * @author jeroen
 */
class TestModel extends Fizzy_Storage_Model
{
    protected $_type = 'test';

    protected $_title = null;
    protected $_body = null;


    public function __construct($title = null, $body = null)
    {
        $this->_title = $title;
        $this->_body = $body;
    }

    public function setTitle($title)
    {
        $this->_title = $title;
    }

    public function populate($array)
    {
        $this->_id = $array['id'];
        $this->_title = $array['title'];
        $this->_body = $array['body'];
    }
    
    public function toArray()
    {
        return array('title' => $this->_title, 'body' => $this->_body);
    }
}
