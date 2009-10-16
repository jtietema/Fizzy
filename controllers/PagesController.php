<?php
/**
 * Class PagesController
 */

/** Fizzy_Controller */
require_once 'Fizzy/Controller.php';

/**
 * Pages controller.
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class PagesController extends Fizzy_Controller
{

    public function defaultAction()
    {
        echo "<p>This is PagesController::defaultAction().</p>";
        
        echo "<p>Parameters:<br />";
        echo "<pre>";
        print_r($this->_getParams());
        echo "</pre></p>";
    }

    public function showAction()
    {
        echo "<p>This is PagesController::showAction().</p>";

        echo "<p>Parameters:<br />";
        echo "<pre>";
        print_r($this->_getParams());
        echo "</pre></p>";
    }

    public function listAction()
    {
        echo "<p>This is PagesController::listAction().</p>";

        echo "<p>Parameters:<br />";
        echo "<pre>";
        print_r($this->_getParams());
        echo "</pre></p>";
    }

    public function slugAction()
    {
        echo "<p>This is PagesController::slugAction().</p>";
        
        echo "<p>Parameters:<br />";
        echo "<pre>";
        print_r($this->_getParams());
        echo "</pre></p>";
    }

}