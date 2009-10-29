<?php
/**
 * Class AdminMenuController
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
require_once 'SecureController.php';

class AdminMenuController extends SecureController
{

    public function defaultAction()
    {
    }

    public function showAction()
    {
        $config = Fizzy_Config::getInstance();
        $storageOptions = $config->getSection('storage');
        $this->_storage = new Fizzy_Storage($storageOptions);

        $pages = $this->_storage->fetchAll('page');
        $this->getView()->pages = $pages;
        $this->getView()->setScript('admin/menu.phtml');
    }
}
