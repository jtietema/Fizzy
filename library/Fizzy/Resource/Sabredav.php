<?php

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