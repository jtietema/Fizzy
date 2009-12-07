<?php
/**
 * Class Fizzy_Storage_Backend_Xml
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

/** Fizzy_Storage_Backend_Abstract */
require_once 'Fizzy/Storage/Backend/Abstract.php';

/** Fizzy_Storage_Backend_Xml_DOMDocument */
require_once 'Fizzy/Storage/Backend/Xml/DOMDocument.php';

/**
 * Storage backend for Fizzy based on XML files.
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Storage_Backend_Xml extends Fizzy_Storage_Backend_Abstract
{
    /**
     * @see Fizzy_Storage_Backend_Abstract
     */
    protected $_identifierField = 'uid';

    /**
     * The path to the container store.
     * @var string
     */
    protected $_dataPath = null;

    /**
     * References to the XML documents for the containers.
     * @var array
     */
    protected $_xmlDocuments = array();

    /** **/

    /**
     * Constructor
     * @todo Add check for relative paths.
     * @param array $options
     * @throws Fizzy_Storage_Exception
     */
    public function __construct($options = array())
    {
        parent::__construct($options);

        $dsn = $this->getDsn();
        list($protocol, $dataPath) = explode(':', $dsn);

        // Change path to system directory separator
        $dataPath = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $dataPath);
        // Add a trailing slash
        $dataPath = rtrim($dataPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        // Check if path is relative within the web application root
        if(0 !== strpos($dataPath, DIRECTORY_SEPARATOR))
        {
            $basePath = Fizzy_Config::getInstance()->getSectionValue(Fizzy_Config::SECTION_APPLICATION, 'basePath');
            $dataPath = $basePath . $dataPath;
        }

        // Set the dsn with the corrected absolute or relative path
        $this->setDsn($protocol . ':' . $dataPath);
        
        if(!is_dir($dataPath) || !is_writable($dataPath)) {
            require_once 'Fizzy/Storage/Exception.php';
            throw new Fizzy_Storage_Exception(
                "Data directory {$dataPath} does not exist or is not writable."
            );
        }
        
        $this->_dataPath = $dataPath;
    }

    /**
     * Returns the XML Document for a container.
     * @param string $container
     * @return Fizzy_Storage_Backend_XmlDocument
     */
    protected function _getXmlDocument($container)
    {
        $container = strtolower(trim($container));
        if(!array_key_exists($container, $this->_xmlDocuments)) {
            $documentPath = $this->_dataPath . $container . '.xml';
            
            if(!is_file($documentPath)) {
                require_once 'Fizzy/Storage/Exception.php';
                throw new Fizzy_Storage_Exception("Container {$documentPath} could not be found.");
            }

            $this->_xmlDocuments[$container] = $this->_loadXmlDocument($documentPath);
        }

        return $this->_xmlDocuments[$container];
    }

    /**
     * Loads an XML document from a path.
     * @param string $path
     */
    protected function _loadXmlDocument($path)
    {
        return new Fizzy_Storage_Backend_Xml_DOMDocument($path);
    }

    /**
     * Parses a DOMNodeList and resturns an array.
     * @param DOMNodeList $nodeList
     * @return array
     */
    protected function _parseNodeList($nodeList)
    {
        $elements = array();
        foreach($nodeList as $node) {
            $simpleXMLElement = simplexml_import_dom($node);
            $elementArray = $this->_elementToArray($simpleXMLElement);
            
            // Remove the identifier from the data
            $identifier = $elementArray[$this->_identifierField];
            unset($elementArray[$this->_identifierField]);
            
            $elements[$identifier] = $elementArray;
        }
        
        return $elements;
    }

    /**
     * Creates a DOMNodeList from an array naming the nodes like $nodeName.
     * @param array $dataArray
     * @param string $nodeName
     * @return DOMNodeList
     */
    protected function _createNodeList(array $dataArray, $document, $nodeName = 'item')
    {
        $root = $document->createElement('container');

        foreach($dataArray as $identifier => $data)
        {
            // Create the element
            $child = $document->createElement($nodeName);
            // Add the identifier as attribute
            $child->setAttribute($this->_identifierField, $identifier);

            // Loop the data columns and add them as CDATA nodes
            foreach($data as $key => $value)
            {
                $dataElement = $document->createElement($key);
                $cdataElement = $document->createCDATASection($value);
                $dataElement->appendChild($cdataElement);
                $child->appendChild($dataElement);
            }

            $root->appendChild($child);
        }
        
        $nodeList = $root->childNodes;
        
        return $nodeList;
    }

    /**
     * Converts a SimpleXMLElement to an array.
     * @param SimpleXMLElement $element
     * @return array
     */
    protected function _elementToArray(SimpleXMLElement $element)
    {
        $data = array();
        if(count($element->children()) > 0) {
            foreach($element->children() as $childKey => $childValue) {
                $value = $this->_elementToArray($childValue);

                $data[$childKey] = $value;
            }
        }
        else {
            $data = (string) $element;
        }
        // account for attributes
        if(count($element->attributes()) > 0) {
            $attributesArray = array();
            foreach($element->attributes() as $attributeKey => $attributeValue) {
                $attributesArray[$attributeKey] = (string) $attributeValue;
            }
            if(!is_array($data)) {
                $data = array('data' => $data);
            }
            $data = array_merge($data, $attributesArray);
        }

        return $data;
    }

    /* Implementation of Fizzy_Storage_Backend_Interface */

    /**
     * @see Fizzy_Storage_Backend_Interface
     */
    public function persist($container, $data, $identifier = null)
    {
        $document = $this->_getXmlDocument($container);
        $nodeList = $document->getAllNodes();

        $elements = $this->_parseNodeList($nodeList);

        // Create a new identifier if empty
        if(null === $identifier)
        {
            list($micro, $seconds) = explode(' ', microtime());
            $identifier = $seconds . substr(strrchr($micro, '.'), 1);
        }

        $elements[$identifier] = $data;
        // Create a new node list from the data
        $nodeList = $this->_createNodeList($elements, $document, $container);
        
        // Write the entire node list to the container file.
        $success = $document->writeNodeList($nodeList);

        return ((boolean) false !== $success);
    }

    /**
     * @see Fizzy_Storage_Backend_Interface
     */
    public function delete($container, $identifier)
    {
        $document = $this->_getXmlDocument($container);
        $nodeList = $document->getAllNodes();

        $elements = $this->_parseNodeList($nodeList);

        // Unset the element
        unset($elements[$identifier]);

        // Create a new node list from the data
        $nodeList = $this->_createNodeList($elements, $document, $container);

        // Write the entire node list to the container file.
        $success = $document->writeNodeList($nodeList);

        return ((boolean) false !== $success);
    }

    /**
     * @see Fizzy_Storage_Backend_Interface
     */
    public function fetchAll($container)
    {
        $document = $this->_getXmlDocument($container);
        $nodeList = $document->getAllNodes();
        $elements = $this->_parseNodeList($nodeList);
        
        return $elements;
    }

    /**
     * @see Fizzy_Storage_Backend_Interface
     */
    public function fetchByIdentifier($container, $identifier)
    {
        if(empty($identifier)) {
            require_once 'Fizzy/Storage/Exception.php';
            throw new Fizzy_Storage_Exception('Identifier cannot be empty.');
        }

        $document = $this->_getXmlDocument($container);
        $nodeList = $document->getElementsByXpath("/container/{$container}[@uid='{$identifier}']");
        $elements = $this->_parseNodeList($nodeList);

        return $elements;
    }

    /**
     * @see Fizzy_Storage_Backend_Interface
     */
    public function fetchByColumn($container, array $columns)
    {
        $document = $this->_getXmlDocument(strtolower($container));

        $whereColumns = array();
        foreach($columns as $key => $value) {
            $whereColumns[$key] = "{$key}='{$value}'";
        }
        
        $nodeList = $document->getElementsByXpath("/container/{$container}[" . implode(' and ', $whereColumns) . "]");
        $elements = $this->_parseNodeList($nodeList);
        
        return $elements;
    }

}