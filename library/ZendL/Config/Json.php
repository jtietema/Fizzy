<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category  Zend
 * @package   Zend_Config
 * @copyright Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id: Xml.php 19059 2009-11-19 20:05:27Z jan $
 */

/**
 * @see Zend_Config
 */
require_once 'Zend/Config.php';

/**
 * @see Zend_Json
 */
require_once 'Zend/Json.php';

/**
 * XML Adapter for Zend_Config
 *
 * @category  Zend
 * @package   Zend_Config
 * @copyright Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendL_Config_Json extends Zend_Config
{
    const EXTENDS_NAME = "_extends";
    /**
     * Whether to skip extends or not
     *
     * @var boolean
     */
    protected $_skipExtends = false;

    /**
     * Loads the section $section from the config file encoded as JSON
     *
     * Sections are defined as properties of the main object
     *
     * In order to extend another section, a section defines the "_extends"
     * property having a value of the section name from which the extending
     * section inherits values.
     *
     * Note that the keys in $section will override any keys of the same
     * name in the sections that have been included via "_extends".
     *
     * @param  string  $json     JSON file or string to process
     * @param  mixed   $section Section to process
     * @param  boolean $options Whether modifiacations are allowed at runtime
     * @throws Zend_Config_Exception When JSON text is not set or cannot be loaded
     * @throws Zend_Config_Exception When section $sectionName cannot be found in $json
     */
    public function __construct($json, $section = null, $options = false)
    {
        if (empty($json)) {
            require_once 'Zend/Config/Exception.php';
            throw new Zend_Config_Exception('Filename is not set');
        }

        $allowModifications = false;
        if (is_bool($options)) {
            $allowModifications = $options;
        } elseif (is_array($options)) {
            if (isset($options['allowModifications'])) {
                $allowModifications = (bool) $options['allowModifications'];
            }
            if (isset($options['skipExtends'])) {
                $this->_skipExtends = (bool) $options['skipExtends'];
            }
        }

        set_error_handler(array($this, '_loadFileErrorHandler')); // Warnings and errors are suppressed
        if ($json[0] != '{') {
            $json = file_get_contents($json);
        }
        restore_error_handler();
        // Check if there was a error while loading file
        if ($this->_loadFileErrorStr !== null) {
            require_once 'Zend/Config/Exception.php';
            throw new Zend_Config_Exception($this->_loadFileErrorStr);
        }

        $config = Zend_Json::decode($json);
        
        if($config == null) {
            // decode failed
            require_once 'Zend/Config/Exception.php';
            throw new Zend_Config_Exception("Error parsing JSON data");
        }

        if ($section === null) {
            $dataArray = array();
            foreach ($config as $sectionName => $sectionData) {
                $dataArray[$sectionName] = $this->_processExtends($config, $sectionName);
            }

            parent::__construct($dataArray, $allowModifications);
        } else if (is_array($section)) {
            $dataArray = array();
            foreach ($section as $sectionName) {
                if (!isset($config->$sectionName)) {
                    require_once 'Zend/Config/Exception.php';
                    throw new Zend_Config_Exception("Section '$sectionName' cannot be found in the data");
                }

                $dataArray = array_merge($this->_processExtends($config, $sectionName), $dataArray);
            }

            parent::__construct($dataArray, $allowModifications);
        } else {
            if (!isset($config[$section])) {
                require_once 'Zend/Config/Exception.php';
                throw new Zend_Config_Exception("Section '$section' cannot be found in the data");
            }

            $dataArray = $this->_processExtends($config, $section);
            if (!is_array($dataArray)) {
                // Section in the JSON data contains just one top level string
                $dataArray = array($section => $dataArray);
            }

            parent::__construct($dataArray, $allowModifications);
        }

        $this->_loadedSection = $section;
    }

    /**
     * Helper function to process each element in the section and handle
     * the "_extends" inheritance attribute.
     *
     * @param  array		    $data Data array to process
     * @param  string           $section Section to process
     * @param  array            $config  Configuration which was parsed yet
     * @throws Zend_Config_Exception When $section cannot be found
     * @return array
     */
    protected function _processExtends(array $data, $section, array $config = array())
    {
        if (!isset($data[$section])) {
            require_once 'Zend/Config/Exception.php';
            throw new Zend_Config_Exception("Section '$section' cannot be found");
        }

        $thisSection  = $data[$section];

        if (isset($thisSection[self::EXTENDS_NAME])) {
            $this->_assertValidExtend($section, $thisSection[self::EXTENDS_NAME]);

            if (!$this->_skipExtends) {
                $config = $this->_processExtends($data, $thisSection[self::EXTENDS_NAME], $config);
            }
            unset($thisSection[self::EXTENDS_NAME]);
        }

        $config = $this->_arrayMergeRecursive($config, $thisSection);

        return $config;
    }
}
