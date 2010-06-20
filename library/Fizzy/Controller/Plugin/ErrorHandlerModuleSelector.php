<?php
/**
 * Class Fizzy_Controller_Plugin_ErrorHandlerModuleSelector
 * @package Fizzy_Controller
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

/**
 * Controller plugin for setting the module specific errorhandler after the
 * routing took place
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
class Fizzy_Controller_Plugin_ErrorHandlerModuleSelector
    extends Zend_Controller_Plugin_Abstract
{
    public function  routeShutdown(Zend_Controller_Request_Abstract $request) {
        $front = Zend_Controller_Front::getInstance();

        $errorHandler = $front->getPlugin('Zend_Controller_Plugin_ErrorHandler');

        if ($errorHandler instanceof Zend_Controller_Plugin_ErrorHandler) {
            $errorHandler->setErrorHandlerModule($request->getModuleName());
        }
    }
}
