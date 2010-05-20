<?php
/**
 * Class Fizzy_Autoloader
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
 * @copyright Copyright (c) 2009-2010 Voidwalkers (http://www.voidwalkers.nl)
 * @license http://www.voidwalkers.nl/license/new-bsd The New BSD License
 */

/**
 * Autoloading class for Fizzy models.
 * 
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Autoloader
{

    /**
     * Autoload function for model classes
     * @param string $class
     * @return boolean
     */
    public static function models($class)
    {
        $modelFile = ucfirst($class) . '.php';
        $config = Zend_Registry::get('config');
        $modelDirectory = $config->paths->models;
        $modelsPath = $modelDirectory . DIRECTORY_SEPARATOR . $modelFile;
        if(is_file($modelsPath)) {
            require $modelsPath;
            return true;
        }

        return false;
    }
}