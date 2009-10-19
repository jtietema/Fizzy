<?php
/**
 * Class Fizzy_View
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
 * Fizzy View class for rendering view scripts and layouts.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_View
{

    /**
     * Base path for view and layout paths.
     * @var string
     */
    private $_basePath = '';
    
    /**
     * Directory containing view scripts.
     * @var string
     */
    private $_scriptPath = '';

    /**
     * Directory containing layout scripts.
     * @var string
     */
    private $_layoutPath = '';

    /**
     * The view script to render.
     * @var string
     */
    private $_script = '';

    /**
     * The layout to use when rendering.
     * @var string
     */
    private $_layout = '';

    /**
     * Whether the view is rendered.
     * @var boolean
     */
    private $_rendered = false;

    /**
     * If the view is enabled and should be rendered.
     * @var boolean
     */
    private $_enabled = true;

    /** **/

    /**
     * Set the base path for view and layout scripts.
     * @param string $path
     * @return Fizzy_View
     */
    public function setBasePath($path)
    {
        $this->_basePath = $path;

        return $this;
    }

    /**
     * Returns the base path for view and layout scripts.
     * @return string
     */
    public function getBasePath()
    {
        return $this->_basePath;
    }

    /**
     * Sets the path containing the view scripts.
     * @param string $path
     * @return Fizzy_View
     */
    public function setScriptPath($path)
    {
        $this->_scriptPath = $path;

        return $this;
    }

    /**
     * Gets the path containing the view scripts.
     * @return string
     */
    public function getScriptPath()
    {
        return $this->_scriptPath;
    }

    /**
     * Sets the path containing the layout scripts.
     * @param string $path
     * @return FIzzy_View
     */
    public function setLayoutPath($path)
    {
        $this->_layoutPath = $path;

        return $this;
    }

    /**
     * Returns the path containing the layout scripts.
     * @return string
     */
    public function getLayoutPath()
    {
        return $this->_layoutPath;
    }

    /**
     * Sets the view script to render.
     * @param string $script
     * @return Fizzy_View
     */
    public function setScript($script)
    {
        $this->_script = $script;

        return $this;
    }

    /**
     * Returns the script to be rendered.
     * @return string
     */
    public function getScript()
    {
        return $this->_script;
    }

    /**
     * Set the layout to use when rendering.
     * @param string $layout
     * @return Fizzy_View
     */
    public function setLayout($layout)
    {
        $this->_layout = $layout;

        return $this;
    }

    /**
     * Returns the layout used in rendering.
     * @return string
     */
    public function getLayout()
    {
        return $this->_layout;
    }

    /**
     * Returns if the view was rendered.
     * @return boolean
     */
    public function isRendered()
    {
        return $this->_rendered;
    }

    /**
     * Disables the view for rendering.
     */
    public function disable()
    {
        $this->_enabled = false;
    }

    /**
     * Enables the view for rendering.
     */
    public function enable()
    {
        $this->_enabled = true;
    }

    /**
     * Returns if the view is enabled.
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->_enabled;
    }

    /**
     * Assigns a variable to the view object.
     * @param string $key
     * @param mixed $value
     * @return Fizzy_View
     */
    public function assign($key, $value)
    {
        $this->$key = $value;

        return $this;
    }

    /**
     * Renders the view script and outputs the result.
     * @return string
     */
    public function render() {

        $viewScript = $this->_script($this->getScript());
        
        ob_start();
        include $viewScript;
        $output = ob_get_clean();

        $this->_rendered = true;

        return $output;
    }

    /**
     * Finds a script name in the view script path.
     * @param string $name
     * @return string
     */
    public function _script($name)
    {
        $viewScript = implode(DIRECTORY_SEPARATOR, array($this->_basePath, $this->_scriptPath, $name));

        if(!is_file($viewScript)) {
            require_once 'Fizzy/Exception.php';
            throw new Fizzy_Exception("View script {$name} could not be found in path {$this->_scriptPath}.");
        }

        return $viewScript;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function __isset($name) 
    {
        return isset($this->$name);
    }

    public function __unset ($name)
    {
        unset($this->$name);
    }
    
}