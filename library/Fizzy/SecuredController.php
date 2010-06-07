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
 * @copyright Copyright (c) 2009-2010 Voidwalkers (http://www.voidwalkers.nl)
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

    /**
     * The session namespace used for the security credentials
     * @var string
     */
    protected $_sessionNamespace = 'fizzy';

    /**
     * Route or url to redirect to when not logged in
     * @var string
     */
    protected $_redirect = '@admin_login';
    /** **/
    
    /**
     * Check for authentication identity
     */
    public function preDispatch()
    {
        if ($this->_sessionNamespace === null || $this->_redirect === null) {
            throw new Fizzy_Exception('Please provide a $_sessionNamespace and $_redirect in your Fizzy_SecuredController subclass.');
        }
        $auth = Zend_Auth::getInstance();
        $auth->setStorage(new Zend_Auth_Storage_Session($this->_sessionNamespace));

        // Redirect when no identity is present
        if (!$auth->hasIdentity()) {
            $this->_redirect($this->_redirect);
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