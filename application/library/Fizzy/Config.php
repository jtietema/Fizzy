<?php
/**
 * Class Fizzy_Config
 * @package Fizzy
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
 * Config class for Fizzy.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Config
{
    /*
     * Constants for default sections.
     */
    const SECTION_APPLICATION = 'application';
    const SECTION_PATHS = 'paths';
    const SECTION_ROUTER = 'router';

    /**
     * Fizzy_Config instance.
     * @var Fizzy_Config
     */
    protected static $_instance = null;

    /**
     * Configuration for Fizzy containing default values.
     * @var array
     */
    protected $_configuration = array(
        self::SECTION_APPLICATION => array(
            'title' => 'Fizzy',
            'environment' => 'production',
            'basePath' => '',
            'defaultLayout' => 'default',
            'defaultTemplate' => 'page',
        ),
        self::SECTION_ROUTER => array (
            'defaultController' => 'default',
            'defaultAction' => 'default'
        ),
        self::SECTION_PATHS => array(
            'base' => '',
            'application' => 'application',
            'controllers' => 'application/controllers',
            'models' => 'application/models',
            'views' => array(
                'application' => 'application/views/scripts',
                'custom' => 'custom/views'
            ),
            'layouts' => array(
                'application' => 'application/views/layouts',
                'custom' => 'custom/layouts',
            ),
            'templates' => array (
                'application' => 'application/views/scripts',
                'custom' => 'custom/templates'
            ),
            'configs' => 'configs',
            'custom' => 'custom',
            'data' => 'data',
            'library' => 'library',
            'public' => 'public',
        ),
    );

    /** **/

    /**
     * Fizzy_Config is a singleton, use Fizzy_Config::getInstance() to obtain
     * an instance.
     */
    protected function __construct()
    {}

    /**
     * Returns the active Fizzy_Config instance.
     * @return Fizzy_Config
     */
    public static function getInstance()
    {
        if(null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Returns the complete configuration as an array.
     * @return array
     */
    public function getConfiguration()
    {
        return $this->_configuration;
    }

    /**
     * Sets the configuration section with the given data. Overrides any
     * existent data in the section.
     * @param string $section
     * @param mixed $data
     * @return Fizzy_Config
     */
    public function setSection($section, $data)
    {
        $this->_configuration[$section] = $data;

        return $this;
    }

    /**
     * Merges values from a section with given key => value array.
     * @param string $section
     * @param array $data
     * @return Fizzy_Config
     */
    public function mergeSection($section, array $data)
    {
        $sectionData = $this->getSection($section);

        if(null === $sectionData) {
            $sectionData = $data;
        } else {
            $sectionData = $this->_arrayMergeRecursive($sectionData, $data);
        }

        return $this->setSection($section, $sectionData);
    }

    /**
     * Sets a value inside a section. The section is created if it does not
     * exist.
     * @param string $section
     * @param string $key
     * @param mixed $value
     * @return Fizzy_Config
     */
    public function setSectionValue($section, $key, $value)
    {
        $sectionData = $this->getSection($section);

        if(null === $sectionData) {
            $sectionData = array();
        }

        $sectionData[$key] = $value;
        $this->setSection($section, $sectionData);

        return $this;
    }

    /**
     * Returns a configuration section by name.
     * @param string $section
     * @return array|null
     */
    public function getSection($section)
    {
        if(array_key_exists($section, $this->_configuration)) {
            return $this->_configuration[$section];
        }

        return null;
    }

    /**
     * Sets a path within the application. Paths must be set relative to the
     * application root. The configured base path will be stripped from
     * set paths.
     * @param string $alias
     * @param string|array $path
     * @return Fizzy_Config
     */
    public function setPath($alias, $path)
    {
        $basePath = $this->_configuration[self::SECTION_APPLICATION]['basePath'];

        if(is_array($path)) {
            $subPaths = array();
            foreach($path as $subAlias => $subPath) {
                if(0 === strpos($subPath, $basePath)) {
                    $subPaths[$alias] = str_replace($basePath, '', $subPath);
                }
            }
            $path = $subPaths;
        }
        else if('base' !== $alias) {
            if(0 === strpos($path, $basePath)) {
                $path = str_replace($basePath, '', $path);
            }
        }

        $this->_configuration[self::SECTION_PATHS][$alias] = $path;

        return $this;
    }
    
    /**
     * Returns a set path by name. Paths are returned include the set base path.
     * Use getApplicationPath($alias) to get the path relative to the
     * application root.
     * @param string $alias
     * @return string|null
     */
    public function getPath($alias)
    {
        if(!array_key_exists($alias, $this->_configuration[self::SECTION_PATHS])) {
            return null;
        }
        
        $basePath = $this->_configuration[self::SECTION_APPLICATION]['basePath'];
        $path = $this->_configuration[self::SECTION_PATHS][$alias];

        if(is_array($path)) {
            $subPaths = array();
            foreach($path as $subAlias => $subPath) {
                if(0 !== strpos($subPath, $basePath)) {
                    $subPaths[$subAlias] = implode(DIRECTORY_SEPARATOR, array($basePath, $subPath));
                }
            }
            $path = $subPaths;
        }
        else {
            if(0 !== strpos($path, $basePath)) {
                $path = implode(DIRECTORY_SEPARATOR, array($basePath, $path));
            }
        }

        return $path;
    }

    /**
     * Merges given paths with existent paths.
     * @param array $paths
     * @return Fizzy_Config
     */
    public function addPaths(array $paths)
    {
        return $this->mergeSection(self::SECTION_PATHS, $paths);
    }

    /**
     * Loads configuration from a SimpleXMLElement.
     * @param SimpleXMLElement $element
     * @return Fizzy_Config
     */
    public function loadConfiguration(SimpleXMLElement $element)
    {
        // Parse the config element
        $dataArray = array();
        foreach($element->children() as $sectionName => $sectionData) {
            $dataArray[$sectionName] = $this->_elementToArray($sectionData);
        }

        $this->_configuration = $this->_arrayMergeRecursive($this->_configuration, $dataArray);

        return $this;
    }

    /**
     * Loads routes configuration from a SimpleXMLElement. Merges the routes
     * with the router section of the configuration.
     * @param SimpleXMLElement $routes
     * @return Fizzy_Config
     */
    public function loadRoutes(SimpleXMLElement $routes)
    {
        $routesArray = array();
        foreach($routes->children() as $routeName => $routeData) {
            $routesArray[$routeName] = $this->_elementToArray($routeData);
        }

        $routerConfig = $this->getSection(self::SECTION_ROUTER);
        $routerConfig = $this->_arrayMergeRecursive($routerConfig, $routesArray);

        $this->setSection(self::SECTION_ROUTER, $routerConfig);

        return $this;
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
                
                // account for attributes
                if(count($childValue->attributes()) > 0) {
                    $attributesArray = array();
                    foreach($childValue->attributes() as $attributeKey => $attributeValue) {
                        $attributesArray[$attributeKey] = (string) $attributeValue;
                    }
                    if(!is_array($value)) {
                        $value = array('value' => $value);
                    }
                    $value = array_merge($value, $attributesArray);
                }
                
                $data[$childKey] = $value;
            }
        }
        else {
            $data = (string) $element;
        }

        return $data;
    }

    /**
     * Merges values from $valueArray into $baseArray recursively overwriting
     * values in $baseArray.
     *
     * @param  mixed $baseArray
     * @param  mixed $valueArray
     * @return array
     */
    protected function _arrayMergeRecursive($baseArray, $valueArray)
    {
        if(is_array($baseArray) && is_array($valueArray)) {
            foreach($valueArray as $key => $value) {
                if(isset($baseArray[$key])) {
                    $baseArray[$key] = $this->_arrayMergeRecursive($baseArray[$key], $value);
                } else {
                    if($key === 0) {
                        $baseArray = array(0 => $this->_arrayMergeRecursive($baseArray, $value));
                    } else {
                        $baseArray[$key] = $value;
                    }
                }
            }
        } else {
            $baseArray = $valueArray;
        }

        return $baseArray;
    }

    /**
     * Override to call configuration sections dynamically.
     * @param string $name
     * @param array $arguments
     */
    public function __call($name, $arguments)
    {
        if(0 === strpos($name, 'get')) {
            $section = str_replace('get', '', $name);
            $section = strtolower(substr($section, 0, 1)) . substr($section, 1, strlen($section));
            return $this->getSection($section);
        }
    }
}
