<?php
/**
 * Class Fizzy_Form
 * @category Fizzy
 * @package Fizzy_Form
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

/** Zend_Form */
require_once 'Zend/Form.php';

/**
 * Fizzy form is an extension of the standard Zend_Form to load Fizzy
 * form elements, decorators and validators.
 *
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Form extends Zend_Form
{
    /**
     * Hook into the init mehtod call to load Fizzy form elements, decorators and
     * validators.
     */
    public function init()
    {
        $this->addPrefixPath('Fizzy_Form', 'Fizzy/Form');
        $this->addElementPrefixPath('Fizzy_Validate', 'Fizzy/Validate', 'validate');
        $this->addElementPrefixPath('Fizzy_Filter', 'Fizzy/Filter', 'filter');

        // Set defautl HtmlTag decorator to div
        $this->setDecorators(array(
            'FormElements',
            array(array('formDiv' => 'HtmlTag'), array('tag' => 'div', 'class' => 'fizzy-form')),
            'Form',
        ));
        $this->setElementDecorators(array(
            array(array('viewHelper' => 'ViewHelper'), array('view' => $this->getView())),
            'Label',
            'Errors',
            array(array('elementDiv' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-row')),
        ));
    }
}