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
        echo "This is PagesController::defaultAction()";
    }

    public function showAction()
    {
        echo "This is PagesController::showAction()";

        var_dump($this->_getParams());
    }

    public function listAction()
    {
        echo "This is PagesController::listAction()";
    }

}