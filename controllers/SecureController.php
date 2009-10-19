<?php
/**
 * Class SecureController
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
 
require_once 'Fizzy/Controller.php';
require_once 'Fizzy/Storage.php';
require_once 'User.php';

/**
 * A controller secured by login
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
class SecureController extends Fizzy_Controller
{

    protected $_requestUri = null;

    protected function _init()
    {
        session_start('fizzy_session');

        if (!isset($_SESSION['username']) && empty($_SESSION['username']))
        {
            $this->_request->setAction('login');
            $this->_requestUri = $this->_request->getRequestUri();
        }
    }

    public function loginAction()
    {
        if ($this->_request->getMethod() === Fizzy_Request::METHOD_POST)
        {
            // perform login
            $config = Fizzy_Config::getInstance();
            $storageOptions = $config->getConfiguration('storage');
            $storage = new Fizzy_Storage($storageOptions['dsn']);

            $model = $storage->fetchColumn('user', 'username', $_POST['username']);
            if ($model !== null && $model->getPassword() === md5($_POST['password']))
            {
                // doe header redirect naar index pagina
                $_SESSION['username'] = $model->getUsername();
                header('Location: http://' . $this->_request->getServerName() . $this->_requestUri);
                die();
            }
            $this->_view->setScript('denied.phtml');
            
        } else {
            $this->_view->url = $this->_request->getRequestUri();
            $this->_view->setScript('login.phtml');
        }
    }
}
