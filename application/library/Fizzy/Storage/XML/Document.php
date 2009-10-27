<?php
/**
 * Class Fizzy_Storage_XML_Document
 * @package Fizzy
 * @subpackage Storage
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
 * @copyright Copyright (c) 2009 Voidwalkers (http://www.voidwalkers.nl)
 * @license http://www.voidwalkers.nl/license/new-bsd The New BSD License
 */

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

        $cdata = $this->createCDATASection();
        $cdata->appendData($value);

        $text = $this->createTextNode();
        $text->appendChild($cdata);
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

    /**
     * Returns a single DOMElement by performing an Xpath query.
     * @param string $query
     * @return DOMElement
     */
    public function getElementByXpath($query)
    {
        $xpath = new DOMXPath($this);
        $results = $xpath->query($query);

        if ($results->length === 0) {
            return null;
        }

        $element = $results->item(0);
        return $element;
    }

    /**
     * Returns a node list by performing an Xpath query.
     * @param string $query
     * @return DOMNodeList
     */
    public function getElementsByXpath($query)
    {
        $xpath = new DOMXPath($this);
        $results = $xpath->query($query);
        
        return $results;
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
