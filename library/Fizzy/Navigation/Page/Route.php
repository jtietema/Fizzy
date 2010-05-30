<?php

/**
 * Description of Route
 *
 * @author jeroen
 */
class Fizzy_Navigation_Page_Route extends Zend_Navigation_Page_Mvc
{
    public function __construct(Array $options)
    {
        if (!isset($options['route']) || empty($options['route'])){
            throw new Zend_Controller_Router_Exception('No routename specified!');
        }
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();
        $route = $router->getRoute($options['route']);
        $defaults = $route->getDefaults();

        $options['module'] = $defaults['module'];
        $options['controller'] = $defaults['controller'];
        $options['action'] = $defaults['action'];

        parent::__construct($options);
    }
}
