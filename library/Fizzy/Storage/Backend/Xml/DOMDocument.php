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
 * @copyright Copyright (c) 2009-2010 Voidwalkers (http://www.voidwalkers.nl)
 * @license http://www.voidwalkers.nl/license/new-bsd The New BSD License
 */

/**
 * DOMDocument extension used by the Fizzy Xml backend.
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Storage_Backend_Xml_DOMDocument extends DOMDocument
{
    /**
     * The file this DOMDocument is
     * @var string
     */
    protected $_file = null;
    
    /** **/

    /**
     * Constructor with added support for filename.
     * @param string $filename
     */
    public function __construct($filename = null)
    {
        parent::__construct();

        if ($filename !== null)
        {
            $this->load($filename);
            $this->_filename = $filename;
        }
    }

    /**
     * Returns all nodes in the document.
     * @return DOMNodeList
     */
    public function getAllNodes()
    {
        return $this->getElementsByXpath('/container/*');
    }

    /**
     * Writes back a node list to the XML document removing any existing nodes.
     * Returns true when document was successfully written, false on failure.
     * @param DOMNodeList $nodes
     * @return boolean
     */
    public function writeNodeList($nodeList)
    {
        // Store the old root for reference
        $oldRoot = $this->documentElement;

        // Create a new root element
        $newRoot = $this->createElement('container');

        /*
         * We have to iterate the node list and copy the nodes to an array because
         * of an error in the DOMNodeList Iterator.
         */
        $newNodes = array();
        foreach($nodeList as $newNode)
        {
            $newNodes[] = $newNode;
        }
        foreach($newNodes as $node)
        {
            $newRoot->appendChild($node);
        }

        // Replace the old root node ('container') with the one containing the new nodes.
        $this->replaceChild($newRoot, $oldRoot);
        $this->formatOutput = true;
        $this->preserveWhitespace = false;
        // Save the document back to file
        return parent::save($this->_filename);
    }

    /**
     * Returns a list of DOMNodes by an Xpath query.
     * @param string $query
     * @return DOMNodeList
     */
    public function getElementsByXpath($query)
    {
        $xpath = new DOMXPath($this);
        $results = $xpath->query($query);

        return $results;
    }
}