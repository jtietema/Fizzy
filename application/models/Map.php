<?php

/**
 * Map
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Map extends BaseMap implements Fizzy_Block_Interface
{

    public function getFormElement()
    {
        return new Fizzy_Form_Element_Maps('block_map_'.$this->id, array(
            'value' => $this->data,
        ));
    }
    public function getValue()
    {
        return $this->data;
    }
    public function setValue($value)
    {
        $this->data = $value;
    }
}