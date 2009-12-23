<?php
/**
 * Class Fizzy_SecuredController
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

/** Fizzy_Controller */
require_once 'Fizzy/Controller.php';

class Fizzy_SecuredController extends Fizzy_Controller
{
    /**
     * Identity object for the autheticated user.
     * @var mixed
     */
    protected $_identity = null;

    /** **/
    
    /**
     * Check for authentication identity
     */
    public function preDispatch()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/fizzy/login', array('prependBase' => true));
        }

        $this->_identity = Zend_Auth::getInstance()->getIdentity();
    }

    /**
     * Returns the identity for the authenticated user.
     * @return mixed
     */
    public function getIdentity()
    {
        return $this->_identity;
    }
}