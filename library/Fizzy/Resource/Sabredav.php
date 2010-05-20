<?php
/**
 * Class Fizzy_Resource_Sabredav
 * @category Fizzy
 * @package Fizzy_Resource
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

class Fizzy_Resource_Sabredav extends Zend_Application_Resource_ResourceAbstract
{

    public function init()
    {
        $options = $this->getOptions();

        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->pushAutoloader(array($this, 'autoload'));
    }

    public static function autoload($className)
    {
        if(strpos($className,'Sabre_')===0) {
            include str_replace('_','/',$className) . '.php';
        }
    }
    
}