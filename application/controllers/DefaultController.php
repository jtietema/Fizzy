<?php
/**
 * Class DefaultController
 * @package Fizzy
 * @subpackage Controller
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