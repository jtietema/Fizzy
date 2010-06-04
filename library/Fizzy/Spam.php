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
 * You can pass any content in the form of an instance of Fizzy_Spam_Document.
 * This class will pass it on to an configured backend and thus provides an
 * unified interface for multiple backends.
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
class Fizzy_Spam
{
    protected static $_defaultAdapter = null;

    protected $_adapter = null;

    public function __construct(Fizzy_Spam_Adapter_Interface $adapter = null)
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

    /**
     * Set the default Adapter to be used when no Adapter is specified in the
     * constructor.
     *
     * @param <type> $adapter
     */
    public static function setDefaultAdapter(Fizzy_Spam_Adapter_Interface $adapter)
    {
        self::$_defaultAdapter = $adapter;
    }

    /**
     * Check if the given document is spam
     * @param Fizzy_Spam_Document $document
     * @return boolean
     */
    public function isSpam(Fizzy_Spam_Document $document)
    {
        return $this->_adapter->isSpam($document);
    }

    /**
     * Submit this document as spam to the backend (feedback)
     *
     * @param Fizzy_Spam_Document $document
     * @return <type> 
     */
    public function submitSpam(Fizzy_Spam_Document $document)
    {
        return $this->_adapter->submitSpam($document);
    }

    /**
     * Submit this document as NOT spam to the backend (feedback)
     * 
     * @param Fizzy_Spam_Document $document
     * @return <type>
     */
    public function submitHam(Fizzy_Spam_Document $document)
    {
        return $this->_adapter->submitHam($document);
    }
}
