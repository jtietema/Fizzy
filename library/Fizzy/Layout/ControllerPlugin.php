<?php
/**
 * Class Fizzy_Layout_ControllerPlugin
 * @category Fizzy
 * @package Fizzy_Layout
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

/** Zend_Controller_Plugin_Abstract */
require_once 'Zend/Controller/Plugin/Abstract.php';

class Fizzy_Layout_ControllerPlugin extends Zend_Controller_Plugin_Abstract
{

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $moduleName = $this->getRequest()->getModuleName();
        if(empty($moduleName)) {
            $moduleName = 'default';
        }

        $layoutPath = APPLICATION_PATH . '/modules/' . $moduleName . '/views/layouts/';

        $layout = Zend_Layout::getMvcInstance();
        
        $layout->setLayoutPath($layoutPath);
    }
    
}