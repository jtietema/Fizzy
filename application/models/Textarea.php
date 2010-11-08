<?php

/**
 * Textarea
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Textarea extends BaseTextarea implements Fizzy_Block_Interface
{

    public function getFormElement()
    {
        return new Zend_Form_Element_Textarea('block_textarea_'.$this->id, array(
            'value' => $this->body,
            'options' => array(
                'cols' => 80
            )
        ));
    }
    public function getValue()
    {
        return $this->body;
    }
    public function setValue($value)
    {
        $this->body = $value;
    }
}