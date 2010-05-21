<?php
/**
 * Class Fizzy_Spam
 * @package Fizzy
 * @subpackage Spam
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

/**
 * Class for filtering (comment) spam using various webservices as backend
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
class Fizzy_Spam
{
    protected static $_defaultAdapter = null;

    protected $_adapter = null;

    public function __construct($adapter = null)
    {
        if (null === $adapter){
            if (null === self::$_defaultAdapter){
                throw new Fizzy_Spam_Exception('No adapter specified.');
            }
            $this->_adapter = self::$_defaultAdapter;
        } else {
            $this->_adapter = $adapter;
        }
        
    }

    public static function setDefaultAdapter($adapter)
    {
        self::$_defaultAdapter = $adapter;
    }

    public function isSpam(Fizzy_Spam_Document $document)
    {
        return $this->_adapter->isSpam($document);
    }

    public function submitSpam(Fizzy_Spam_Document $document)
    {
        return $this->_adapter->submitSpam($document);
    }

    public function submitHam(Fizzy_Spam_Document $document)
    {
        return $this->_adapter->submitHam($document);
    }
}
