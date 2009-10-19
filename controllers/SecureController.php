<?php

require_once 'Fizzy/Controller.php';
require_once 'Fizzy/Storage.php';
require_once 'User.php';

/**
 * Description of SecureController
 *
 * @author jeroen
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
        //var_dump($this->_request);
        if ($this->_request->getMethod() === Fizzy_Request::METHOD_POST)
        {
            // perform login
            $config = Fizzy_Config::getInstance();
            $storageOptions = $config->getConfiguration('storage');
            $storage = new Fizzy_Storage($storageOptions['dsn']);

            //$params = $this->_request->getParameters();

            $model = $storage->fetchColumn('user', 'username', $_POST['username']);
            if ($model !== null && $model->getPassword() === md5($_POST['password']))
            {
                // doe header redirect naar index pagina
                $_SESSION['username'] = $model->getUsername();
                header('Location: http://' . $this->_request->getServerName() . $this->_requestUri);
//                echo 'Location: ' . $this->_request->getServerName() . $this->_requestUri;
                die();
            }
            $this->_view->setScript('denied.phtml');
            
        } else {
            $this->_view->url = $this->_request->getRequestUri();
            $this->_view->setScript('login.phtml');
        }
    }
}
