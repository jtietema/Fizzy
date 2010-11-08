<?php
/**
 * All Block types should implement this interface
 * The Block model uses this interface to relay calls to the Block Type
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 * @copyright Voidwalkers 2010
 */
interface Fizzy_Block_Interface
{
    /**
     * Returns the FormElement used for editing this blocks value
     * @return Zend_Form_Element
     */
    public function getFormElement();

    /**
     * Return the current value from the block
     * @return mixed
     */
    public function getValue();

    /**
     * Set the block value
     * @param $value mixed
     */
    public function setValue($value);
}