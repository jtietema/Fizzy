<?php
/**
 * Class Fizzy_Form_Element_Maps
 * @category Fizzy
 * @package Fizzy_Form_Element_Maps
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

class Fizzy_Form_Element_Maps extends Zend_Form_Element_Xhtml
{
    /**
     * Use formTextarea view helper by default
     * @var string
     */
    public $helper = 'maps';

    public function init()
    {
        $view = $this->getView();
        $view->addHelperPath('Fizzy/View/Helper', 'Fizzy_View_Helper');
    }
}