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

/** Fizzy_ViewHelpers */
require_once 'Fizzy/ViewHelpers.php';

/**
 * Fizzy View class for rendering view scripts and layouts.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_View extends Fizzy_ViewHelpers
{

    /**
     * The request object.
     * @var Fizzy_Request
     */
    protected $_request = null;

    /**
     * Directories containing view scripts.
     * @var array
     */
    protected $_scriptPaths = array();

    /**
     * Directories containing layout scripts.
     * @var array
     */
    protected $_layoutPaths = array();

    /**
     * The view script to render.
     * @var string
     */
    protected $_script = '';

    /**
     * The layout to use when rendering.
     * @var string
     */
    protected $_layout = '';

    /**
     * Whether the view is rendered.
     * @var boolean
     */
    protected $_rendered = false;

    /**
     * If the view is enabled and should be rendered.
     * @var boolean
     */
    protected $_enabled = true;

    /** **/

    /**
     * Sets the request object for the view
     * @param Fizzy_Request $request
     * @return Fizzy_View
     */
    public function setRequest(Fizzy_Request $request)
    {
        $this->_request = $request;

        return $this;
    }

    /**
     * Returns the request object for this view.
     * @return Fizzy_Request
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Sets the directories containing view scripts. Paths must be set as
     * absolute paths.
     * @param array $paths
     * @return Fizzy_View
     */
    public function setScriptPaths(array $paths)
    {
        $this->_scriptPaths = $paths;

        return $this;
    }

    /**
     * Returns the directories containing view scripts.
     * @return array
     */
    public function getScriptPaths()
    {
        return $this->_scriptPaths;
    }

    /**
     * Adds a directory containing view scripts to the stack. Paths added must
     * be absolute paths.
     * @param string $path
     * @return Fizzy_View
     */
    public function addScriptPath($alias, $path)
    {
        $this->_scriptPaths[$alias] = $path;

        return $this;
    }

    /**
     * Sets the directories containing layout scripts. Paths must be set as
     * absolute paths.
     * @param array $paths
     * @return FIzzy_View
     */
    public function setLayoutPaths(array $paths)
    {
        $this->_layoutPaths = $paths;

        return $this;
    }

    /**
     * Returns the directories containing layout scripts.
     * @return array
     */
    public function getLayoutPaths()
    {
        return $this->_layoutPaths;
    }

    /**
     * Adds a directory containing layout scripts to the stack. Path must be
     * added as an absolute path.
     * @param string $alias
     * @param string $path
     * @return Fizzy_View
     */
    public function addLayoutPath($alias, $path)
    {
        $this->_layoutPaths[$alias] = $path;

        return $this;
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
        foreach(array_reverse($this->_scriptPaths) as $alias => $path)
        {
            $viewScript = implode(DIRECTORY_SEPARATOR, array($path, $name));
            if(is_file($viewScript)) {
                return $viewScript;
            }
        }

        // No script found
        require_once 'Fizzy/Exception.php';
        throw new Fizzy_Exception("View script {$name} could not be found. Paths: " . implode('; ', array_reverse($this->_scriptPaths)) . ";");
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