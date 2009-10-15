<?php
/**
 * Fizzy bootstrap file.
 *
 * @copyright Voidwalkers (http://www.voidwalkers.nl)
 * @licence New BSD
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
          ->loadRoutes(simplexml_load_file(CONFIG_DIR .'/routes.xml'));

$frontController = new Fizzy_FrontController($config);
$frontController->dispatch();
