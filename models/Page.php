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

}
