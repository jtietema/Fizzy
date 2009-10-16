<?php
/**
 * Fizzy bootstrap file.
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
 * 
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../'));
define('CONFIG_DIR', APPLICATION_PATH . '/configs');
define('LIBRARY_PATH', APPLICATION_PATH . '/library');
define('CONTROLLER_PATH', APPLICATION_PATH . '/controllers');
define('VIEWS_PATH', APPLICATION_PATH . '/views');

set_include_path(LIBRARY_PATH . PATH_SEPARATOR . get_include_path());
require_once 'Fizzy/Config.php';
require_once 'Fizzy/FrontController.php';

$config = Fizzy_Config::getInstance()
          ->loadConfiguration(simplexml_load_file(CONFIG_DIR .'/fizzy.xml'))
          ->loadConfiguration(simplexml_load_file(CONFIG_DIR .'/routes.xml'));

$frontController = new Fizzy_FrontController($config);
$frontController->dispatch();
