<?php
/**
 * Class SecureController
 * @package Fizzy
 * @subpackage Controller
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

/** Fizzy_Storage */
require_once 'Fizzy/Storage.php';

/**
 * A controller secured by login
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
class SecureController extends Fizzy_Controller
{
    /**
     * The request URI to redirect back to.
     * @var string
     */
    protected $_requestUri = null;

    /** **/
    
    protected function _init()
    {
        session_start('fizzy_session');

        if (!isset($_SESSION['username']) && empty($_SESSION['username']))
        {
            $this->_request->setAction('login');
            $this->_requestUri = $this->_request->getRequestUri();
        }
    }

    /**
     * Checks the user credentials and creates a session.
     */
    public function loginAction()
    {
        $this->getView()->message = '';
        if ($this->_request->getMethod() === Fizzy_Request::METHOD_POST)
        {
            // perform login
            $storage = new Fizzy_Storage(Fizzy_Config::getInstance()->getSection('storage'));
            $users = $storage->fetchByField('User', array('username' => $_POST['username']));
            if(0 < count($users)) {
                $user = array_shift($users);
                if ($user->getPassword() === md5($_POST['password']))
                {
                    // doe header redirect naar index pagina
                    $_SESSION['username'] = $user->getUsername();
                    header('Location: http://' . $this->_request->getServerName() . $this->_requestUri);
                    exit();
                }
                else {
                    $this->getView()->message = 'Incorrect password. Please try again.';
                }
            }
            else {
                $this->getView()->message = 'Username could not be found.';
            }
            
        }
        $this->getView()->url = $this->_request->getRequestUri();
        $this->getView()->setScript('login.phtml');
    }

    /**
     * Destroys the session and redirects the user back to the login screen.
     */
    public function logoutAction()
    {
        session_destroy();
        $this->_redirect('/admin');
    }

    /**
     * Makes sure the admin layout is selected.
     * @see Fizzy_Controller
     */
    public function after()
    {
        if($this->getRequest()->getAction() != 'login') {
            $this->getView()->setLayout('admin');
        }
    }
}
