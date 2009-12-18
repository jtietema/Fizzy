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
define('APPLICATION_PATH', realpath(dirname(__FILE__)));
define('APPLICATION_LIBRARY_PATH', APPLICATION_PATH . DIRECTORY_SEPARATOR . 'library');
define('ROOT_PATH', realpath(APPLICATION_PATH . DIRECTORY_SEPARATOR . '..'));
define('LIBRARY_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'library');
define('CONFIG_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'configs');

# Set include path
set_include_path(LIBRARY_PATH . PATH_SEPARATOR . APPLICATION_LIBRARY_PATH . DIRECTORY_SEPARATOR . get_include_path());

# Bootstrap Fizzy
require_once 'Fizzy/Bootstrap.php';
$bootstrap = new Fizzy_Bootstrap(CONFIG_PATH . DIRECTORY_SEPARATOR . 'fizzy.ini', 'development');
$bootstrap->run(true);

/*
require_once 'Zend/Config/Ini.php';
require_once 'Zend/Controller/Front.php';
require_once 'Zend/Registry.php';
require_once 'Zend/Layout.php';
require_once 'Fizzy/Storage.php';


# Create a front controller
$frontController = Zend_Controller_Front::getInstance();
$frontController->throwExceptions(true);

# Set controllers
$frontController->setControllerDirectory($config->paths->controllers->toArray())
                ->setDefaultControllerName($config->application->defaultController)
                ->setDefaultAction($config->application->defaultAction)
                ->setDefaultModule('application');

# Add routes
$routes = new Zend_Config_Ini(CONFIG_PATH . DIRECTORY_SEPARATOR . 'routes.ini', null, true);
$router = $frontController->getRouter()->addConfig($routes, null);

# Create a new storage instance
$storage = new Fizzy_Storage($config->storage->toArray());
Zend_Registry::set('storage', $storage);

# Set view

# Start Layout
Zend_Layout::startMvc(array(
    'layoutPath' => $config->application->layouts
));


# Dispatch the request
$frontController->dispatch();


$config = Fizzy_Config::getInstance()
          ->loadConfiguration(simplexml_load_file(CONFIG_PATH .'/fizzy.xml'))
          ->loadRoutes(simplexml_load_file(CONFIG_PATH .'/routes.xml'));

$basePath = $config->getSectionValue(Fizzy_Config::SECTION_APPLICATION, 'basePath');
if(empty($basePath))
{
    // Set a default base path
    $config->setSectionValue(Fizzy_Config::SECTION_APPLICATION, 'basePath', ROOT_PATH);
}

// Dispatch the request
$frontController = new Fizzy_FrontController($config);
$frontController->dispatch();
*/