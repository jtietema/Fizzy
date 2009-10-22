<?php
/**
 * Class AdminController
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

    public function after()
    {
        if($this->getRequest()->getAction() != 'login') {
            $this->getView()->setLayout('admin.phtml');
        }
    }

    public function defaultAction()
    {
        $this->_redirect('/admin/pages');
    }
}
