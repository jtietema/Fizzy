<?php

/**
 * Description of Document
 *
 * @author jeroen
 */
class Fizzy_Storage_XML_Document extends DOMDocument
{

    private $_filename = null;

    public function __construct($filename = null)
    {
        parent::__construct();

        if ($filename !== null)
        {
            $this->load($filename);
            $this->_filename = $filename;
        }
    }

    public function addElementWithValue($name, $value, DOMNode $target)
    {
        $element = $this->createElement($name);
        $target->appendChild($element);

        $text = $this->createTextNode($value);
        $target->appendChild($text);

        return $element;
    }

    public function addAttributeWithValue($name, $value, DOMNode $target)
    {
        $attribute = $this->createAttribute($name);
        $target->appendChild($attribute);

        $text = $this->createTextNode($value);
        $attribute->appendChild($text);

        return $attribute;
    }

    public function getElementByUid($id)
    {
        return $this->getElementByXpath("//*[@uid='$id']");
    }

    public function getElementByXpath($query)
    {
        // select the node with the correct id
        $xpath = new DOMXPath($this);
        $results = $xpath->query($query);
        // set the element for futher use
        if ($results->length === 0)
            return null;

        $element = $results->item(0);
        return $element;
    }

    public function save($filename = null)
    {
        
        if ($filename !== null)
        {
            return parent::save($filename);
        }
        elseif ($this->_filename !== null)
        {
            return parent::save($this->_filename);
        }
        else
        {
            throw new Fizzy_Storage_Exception_XMLError("Need a filename.");
        }

    }
}
