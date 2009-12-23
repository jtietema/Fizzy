<?php

class Fizzy_Form_Element_Wysiwyg extends Zend_Form_Element_Xhtml
{
    /**
     * Use formTextarea view helper by default
     * @var string
     */
    public $helper = 'wysiwyg';

    public function init()
    {
        $view = $this->getView();
        $view->addHelperPath('Fizzy/View/Helper', 'Fizzy_View_Helper');
    }
}