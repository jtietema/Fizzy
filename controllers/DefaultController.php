<?php
/**
 * Class DefaultController
 */

/** Fizzy_Controller */
require_once 'Fizzy/Controller.php';

require_once 'Menu.php';

/**
 * Default controller.
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class DefaultController extends Fizzy_Controller
{

    public function defaultAction()
    {
        $this->getView()->title = "Default";
    }

    public function anotherAction()
    {
        $this->getView()->title = "Default";
        $this->getView()->setScript('default/default.phtml');
        $this->getView()->setLayout('fizzy.phtml');
    }

    public function menuAction()
    {
        $this->getView()->disable();

        $menu = new Menu();
        $menu->title = 'Main Menu';
        $menu->setItems(array (
            'Homepage', 'Blog', 'About', 'Contact'
        ));

    }

}