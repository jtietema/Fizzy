<?php
/**
 * User model.
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
 * User model, represents an user in the CMS
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
class User extends Fizzy_Storage_Model
{
    /**
     * @see Fizzy_Storage_Model
     */
    protected $_containerName = 'user';

}
