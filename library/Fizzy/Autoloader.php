<?php

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