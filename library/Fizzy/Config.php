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
    protected $_configuration = array();

    /** **/

    /**
     * Default constructor.
     */
    public function __construct()
    {
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
     * Loads configuration from a SimpleXMLElement.
     * @param SimpleXMLElement $config
     */
    public function loadConfiguration(SimpleXMLElement $config)
    {
        // Parse the config element
        $dataArray = array();
        foreach($config->children() as $sectionName => $sectionData) {
            $dataArray[$sectionName] = $this->_elementToArray($sectionData);
        }

        $this->_configuration = array_merge($this->_configuration, $dataArray);
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
