<?php
/**
 * Class DefaultController
 */

/** Fizzy_Controller */
require_once 'Fizzy/Controller.php';

/**
 * Default controller.
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class DefaultController extends Fizzy_Controller
{

    public function defaultAction()
    {
        echo "This is DefaultController::defaultAction()";
    }

    public function anotherAction()
    {
        echo "This is DefaultController::anotherAction()";
    }
    
}