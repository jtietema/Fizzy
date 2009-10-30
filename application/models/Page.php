<?php
/**
 * Class Page
 * @package Fizzy
 * @subpackage Model
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

/** Fizzy_Storage_Model */
require_once 'Fizzy/Storage/Model.php';

/**
 * Represents a user contributed page.
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Page extends Fizzy_Storage_Model
{
    /**
     * @see Fizzy_Storage_Model
     */
    protected $_type = 'page';

    /**
     * Template for the page.
     * @var string
     */
    public $template = null;

    /**
     * Layout for the page.
     * @var string
     */
    public $layout = null;

    /** **/

    /**
     * Override to return the default template if none is set.
     * @return string
     */
    public function getTemplate()
    {
        if(empty($this->template)) {
            $application = Fizzy_Config::getInstance()->getSection('application');
            $this->template = $application['defaultTemplate'];
        }

        return $this->template;
    }

    /**
     * Override to return the default layout if none is set.
     * @return string
     */
    public function getLayout()
    {
        if(empty($this->layout)) {
            $application = Fizzy_Config::getInstance()->getSection('application');
            $this->layout = $application['defaultLayout'];
        }

        return $this->layout;
    }

}
