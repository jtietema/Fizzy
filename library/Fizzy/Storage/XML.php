<?php
require_once 'Fizzy/Storage/Interface.php';
require_once 'Fizzy/Storage/Exception/InvalidConfig.php';
require_once 'Fizzy/Storage/XML/Document.php';
require_once 'Fizzy/Storage/Exception/XMLError.php';

/**
 * Storage backend to a XML file.
 *
 * @author jeroen
 */
class Fizzy_Storage_XML implements Fizzy_Storage_Interface
{

    /**
     * Used to store all open XML documents
     * @var array
     */
    protected $_xmlDocuments = null;

    protected $_dataDir = null;

    /**
     * $dsn should be in the form of xml:/path/to/xmldir
     * NOTE: it should not contain file names
     * those are generated based on the model type.
     * 
     * @param string $dsn
     */
    public function __construct($dsn)
    {
        $pieces = explode(':', $dsn);

        if (!is_dir($pieces[1]))
        {
            throw new Fizzy_Storage_Exception_InvalidConfig(
                'Directory ' . $pieces[1] . ' does not exist.'
            );
        }

        $this->_dataDir = dirname($pieces[1] . '/.');
        $this->_xmlDocuments = array();
    }

    public function persist(Fizzy_Model $model)
    {
        $type = $model->getType();
        
        $domDocument = $this->_initXML($type, $model);

        if ($model->getId() === null)
        {
            // create the element containing this model
            $element = $domDocument->createElement($type);

            // create an id for this model
            $id = time();
            $model->setId($id);
            // store the id in XML
            $domDocument->addAttributeWithValue('uid', $id, $element);

            // store all the fields in the element
            $fields = $model->toArray();
            foreach ($fields as $key => $value)
            {
                $keyElement = $domDocument->createElement($key);
                $element->appendChild($keyElement);

                $valueTextNode = $domDocument->createTextNode($value);
                $keyElement->appendChild($valueTextNode);
            }

            // add the element to the root element
            $root = $domDocument->getElementByUid('root');
            $root->appendChild($element);
        }
        else
        {
            // select the node with the correct id
            $element = $domDocument->getElementByUid($model->getId());

            // store all the fields in the element
            $fields = $model->toArray();
            for ($i = 0; $i < $element->childNodes->length; $i++)
            {
                $child = $element->childNodes->item($i);
                $textNode = $child->firstChild;
                $newTextNode = $domDocument->createTextNode($fields[$child->nodeName]);
                $child->replaceChild($newTextNode, $textNode);
            }
        }
        $filename = $this->_filename($model->getType());
        $domDocument->save($filename);
        
        return $model;
    }

    public function remove(Fizzy_Model $model)
    {
        $type = $model->getType();

        $domDocument = $this->_initXML($type, $model);
        
        $element = $domDocument->getElementByUid($model->getId());

        $parent = $element->parentNode;

        $parent->removeChild($element);

        $domDocument->save();

        return true;
    }

    public function fetchOne($type, $uid)
    {
        $domDocument = $this->_initXML($type);

        $element = $domDocument->getElementByUid($uid);

        $simpleXMLElement = simplexml_import_dom($element);

        $array = $this->_elementToArray($simpleXMLElement);

        $array['id'] = $array['uid'];
        unset($array['uid']);
        return $array;
        
    }

    public function fetchAll($type)
    {
        $xml = $this->_initXML($type);

        $root = $xml->getElementByUid('root');

        $results = array();
        for ($i = 0; $i < $root->childNodes->length; $i++)
        {
            $element = $root->childNodes->item($i);

            $simpleXMLElement = simplexml_import_dom($element);
            $array = $this->_elementToArray($simpleXMLElement);

            $array['id'] = $array['uid'];
            unset($array['uid']);
            
            $results[] = $array;
        }

        return $results;
    }

    /**
     * Checks if the XML file was already loaded returns, loads or creates as
     * nesseceray.
     * 
     * @param string $type
     * @return Fizzy_Storage_XML_Document
     */
    protected function _initXML($type, $model = null)
    {
        // check if the document is already loaded
        if (!array_key_exists($type,$this->_xmlDocuments))
        {
            // build the filename (note the append s on the end)
            $filename = $this->_filename($type);

            // check if the file exists
            if (is_file($filename))
            {
                // load existing document
                $this->_xmlDocuments[$type] = $this->_loadXML($filename);
            }
            else
            {
                // we need a model to base our XML on
                if ($model === null)
                {
                    throw new Fizzy_Storage_Exception_XMLError(
                        "Can't create table without a model. " .
                        "Perhaps you are trying to fetch from a not existing table?"
                    );
                }
                // create new document
                $this->_xmlDocuments[$type] = $this->_createXML($filename, $model);
            }
        }

        return $this->_xmlDocuments[$type];
    }

    protected function _createXML($filename, Fizzy_Model $model)
    {
        // create the root
        $domDocument = new Fizzy_Storage_XML_Document();
        // @TODO: remove hardcoded plural
        $root = $domDocument->createElement($model->getType() . 's');
        $domDocument->appendChild($root);
        $attr = $domDocument->addAttributeWithValue('uid', 'root', $root);
        //$root->setIdAttribute('root', true);
        
        $domDocument->save($filename);

        return $domDocument;
    }

    protected function _loadXML($filename)
    {
        $domDocument = new Fizzy_Storage_XML_Document($filename);

        return $domDocument;
    }

    /**
     * Builds the filename based on the $type
     * 
     * @param string $type
     * @return string
     */
    protected function _filename($type)
    {
        // @TODO: remove hardcoded plural
        return $this->_dataDir . DIRECTORY_SEPARATOR . $type . 's.xml';
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
}
