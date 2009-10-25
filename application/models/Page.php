<?php
/**
 * Class Page
 */

/** Fizzy_Model */
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
     * Tempalte for the page.
     * @var string
     */
    public $template = null;

    /** **/

    /**
     * Override to return the default template.
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

}
