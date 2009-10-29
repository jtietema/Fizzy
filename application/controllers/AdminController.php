<?php
/**
 * Class AdminController
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

/** SecureController */
require_once 'SecureController.php';

/**
 * Description of AdminPagesController
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
class AdminController extends SecureController
{
    
    /**
     * Storage container.
     * @var Fizzy_Storage
     */
    protected $_storage = null;

    /** **/

    public function defaultAction()
    {
        $this->_redirect('/admin/pages');
    }

    /**
     * Show fizzy configuration that was loaded.
     */
    public function configurationAction()
    {
        $this->getView()->configuration = Fizzy_Config::getInstance()->getConfiguration();
        $this->getView()->setScript('admin/configuration.phtml');
    }

    /**
     * Make sure the admin layout is select, except for the login action.
     * @see Fizzy_Controller
     */
    public function after()
    {
        if($this->getRequest()->getAction() != 'login') {
            $this->getView()->setLayout('admin');
        }
    }
}
