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
$bootstrap->run();