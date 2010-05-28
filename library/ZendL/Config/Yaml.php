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
 * XML Adapter for Zend_Config
 *
 * @category  Zend
 * @package   Zend_Config
 * @copyright Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendL_Config_Yaml extends Zend_Config
{
    const EXTENDS_NAME = "_extends";
    /**
     * Whether to skip extends or not
     *
     * @var boolean
     */
    protected $_skipExtends = false;
    
    /**
     * What to call when we need to decode some YAML?
     * 
     * @var callable
     */
    protected $yamlDecoder = array('ZendL_Config_Yaml', 'decode');

    /**
     * Get callback for decoding YAML
     * 
	 * @return callable
	 */
	public function getYamlDecoder() 
	{
		return $this->yamlDecoder;
	}

	/**
	 * Set callback for decoding YAML
	 * 
	 * @param $yamlDecoder the decoder to set
	 * @returns Zend_Config_Yaml
	 */
	public function setYamlDecoder($yamlDecoder) 
	{
	    if(!is_callable($yamlDecoder)) {
            require_once 'Zend/Config/Exception.php';
            throw new Zend_Config_Exception('Invalid parameter to setYamlDecoder - must be callable');
	    }
	    
		$this->yamlDecoder = $yamlDecoder;
		return $this;
	}

    /**
     * Loads the section $section from the config file encoded as YAML
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
     * @param  string  $yaml     YAML file to process
     * @param  mixed   $section Section to process
     * @param  boolean $options Whether modifiacations are allowed at runtime
     */
	public function __construct($yaml, $section = null, $options = false)
    {
        if (empty($yaml)) {
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
            if(isset($options['yamlDecoder'])) {
                $this->setYamlDecoder($options['yamlDecoder']);
            }
        }

        set_error_handler(array($this, '_loadFileErrorHandler')); // Warnings and errors are suppressed
        $yaml = file_get_contents($yaml);
        restore_error_handler();
        // Check if there was a error while loading file
        if ($this->_loadFileErrorStr !== null) {
            require_once 'Zend/Config/Exception.php';
            throw new Zend_Config_Exception($this->_loadFileErrorStr);
        }

        $config = call_user_func($this->yamlDecoder, $yaml);
        
        if($config == null) {
            // decode failed
            require_once 'Zend/Config/Exception.php';
            throw new Zend_Config_Exception("Error parsing YAML data");
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

        if (is_array($thisSection) && isset($thisSection[self::EXTENDS_NAME])) {
            $this->_assertValidExtend($section, $thisSection[self::EXTENDS_NAME]);

            if (!$this->_skipExtends) {
                $config = $this->_processExtends($data, $thisSection[self::EXTENDS_NAME], $config);
            }
            unset($thisSection[self::EXTENDS_NAME]);
        }

        $config = $this->_arrayMergeRecursive($config, $thisSection);

        return $config;
    }
    
    /**
     * Very dumb YAML parser
     * 
     * Until we have Zend_Yaml...
     * 
     * @param string $yaml YAML source
     * @return array Decoded data
     */
    public static function decode($yaml)
    {
        $lines = explode("\n", $yaml);
        reset($lines);
        return self::_decodeYaml(0, $lines);
    }
    
    /**
     * Service function to decode YAML
     * 
     * @param int $this_indent Current indent level
     * @param array $lines  YAML lines
     * @return array|string
     */
    protected static function _decodeYaml($this_indent, &$lines)
    {
        $config = array();
        $know_indent = false;
        while(list($n, $line) = each($lines)) {
            $lineno = $n+1;
            if(strlen($line) == 0) {
                continue;
            }
            if($line[0] == '#') {
                // comment
                continue;
            }
            $indent = strspn($line, " ");
            // line without the spaces
            $line = trim($line);
            if(strlen($line) == 0) {
                continue;
            }
            
            if($indent < $this_indent) {
                // this level is done
                prev($lines);
                return $config;
            }
            
            if(!$know_indent) {
                $this_indent = $indent;
                $know_indent = true; 
            }
            
            if($indent != $this_indent) {
                require_once 'Zend/Config/Exception.php';
                throw new Zend_Config_Exception("Error parsing YAML at line $lineno - unexpected indent");
            }
            
            if(preg_match("/(\w+):\s*(.*)/", $line, $m)) {
                // key: value
                if($m[2]) {
                    // simple key: value
                    $value = $m[2];
                } else {
                    // key: and then values on new lines
                    $value = self::_decodeYaml($this_indent+1, $lines);
                    if(is_array($value) && count($value) == 0) {
                        $value = "";
                    }
                }
                $config[$m[1]] = $value;
            } else if($line[0] == "-") {
                // item in the list:
                // - FOO
                if(strlen($line) > 2) {
                    $config[] = substr($line, 2);
                } else {
                    $config[] = self::_decodeYaml($this_indent+1, $lines);
                }
            } else {
                require_once 'Zend/Config/Exception.php';
                throw new Zend_Config_Exception("Error parsing YAML at line $lineno - unsupported syntax: $line");
            }
        }
        return $config;
    }
}
