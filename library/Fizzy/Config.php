<?php
/**
 * Class Fizzy_Config
 *
 * @copyright Voidwalkers (http://www.voidwalkers.nl)
 * @license New BSD
 * @package Fizzy
 */

/**
 * Config class for Fizzy MVC framework.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Config {
    
    /**
     * Configuration settings.
     * @var array
     */
    protected $_configuration = null;

    /** **/

    /**
     * Default constructor.
     */
    public function __construct()
    {
        $this->_application = array('application', 'routes');
    }

    /**
     * Returns the configuration or a section of it.
     * @param string $section
     * @return array
     */
    public function getConfiguration($section = null)
    {
        if(null === $section) {
            return $this->_configuration;
        }

        if(array_key_exists($section, $this->_configuration)) {
            return $this->_configuration[$section];
        }

        return null;
    }

    /**
     * Loads application configuration.
     * @param SimpleXMLElement $config
     */
    public function loadApplication(SimpleXMLElement $config)
    {
        if(!isset($config->application)) {
            require_once 'Fizzy/Exception.php';
            throw new Fizzy_Exception('No application configuration provided.');
        }
        // Get the section containing the application configuration
        $applicationSection = $config->application;

        // Parse the children
        $dataArray = array();
        foreach($applicationSection->children() as $sectionName => $sectionData) {
            $dataArray[$sectionName] = $this->_elementToArray($sectionData);
        }

        $this->_configuration['application'] = $dataArray;
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
                if(count($childValue->children()) > 0) {
                    $value = $this->_elementToArray($childValue);
                }
                else {
                    $value = (string) $childValue;
                }

                if(count($childValue->attributes()) > 0) {
                    $valueArray = array('value' => $value);
                    foreach($childValue->attributes() as $attributeKey => $attributeValue) {
                        $valueArray[$attributeKey] = (string) $attributeValue;
                    }
                    $value = $valueArray;
                }
                $data[$childKey] = $value;
            }
        }
        else {
            $data = (string) $element;
        }

        return $data;
    }
    
}
