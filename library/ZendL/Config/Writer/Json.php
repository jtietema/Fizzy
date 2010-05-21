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
 * @category   Zend
 * @package    Zend_Config
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Xml.php 18862 2009-11-05 18:25:20Z beberlei $
 */

/**
 * @see Zend_Config_Writer
 */
require_once 'Zend/Config/Writer/FileAbstract.php';

/**
 * @see Zend_Config_Json
 */
require_once 'ZendL/Config/Json.php';

/**
 * @category   Zend
 * @package    Zend_Config
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ZendL_Config_Writer_Json extends Zend_Config_Writer_FileAbstract
{
    /**
     * If we need to pretty-print JSON data
     * 
     * @var boolean
     */
    protected $_prettyPrint = false;
    
    /**
     * Get prettyPrint flag
     * 
	 * @return the prettyPrint flag
	 */
	public function getPrettyPrint() {
		return $this->_prettyPrint;
	}

	/**
	 * Set prettyPrint flag
	 * 
	 * @param $_prettyPrint prettyPrint flag
	 * @return Zend_Config_Writer_Json
	 */
	public function setPrettyPrint($_prettyPrint) {
		$this->_prettyPrint = $_prettyPrint;
		return $this;
	}

    /**
     * Render a Zend_Config into a JSON config string.
     *
     * @since 1.10
     * @return string
     */
	public function render()
    {
        $data        = $this->_config->toArray();
        $sectionName = $this->_config->getSectionName();
        $extends     = $this->_config->getExtends();

        if (is_string($sectionName)) {
            $data = array($sectionName => $data);
        }
        
        foreach($extends as $section => $parent_section) {
            $data[$section][ZendL_Config_Json::EXTENDS_NAME] = $parent_section;
        }

        $out = Zend_Json::encode($data);
        if($this->_prettyPrint) {
             $out = Zend_Json::prettyPrint($out);   
        }
        return $out;
    }
}
