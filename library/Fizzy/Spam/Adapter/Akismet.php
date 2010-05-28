<?php
/**
 * Class Fizzy_Spam_Adapter_Akismet
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
 * Description of Akismet
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
class Fizzy_Spam_Adapter_Akismet implements Fizzy_Spam_Adapter_Interface
{
    protected $_akismet = null;

    public function __construct($apiKey, $blogUrl)
    {
        $this->_akismet = new Zend_Service_Akismet($apiKey, $blogUrl);
    }

    public function isSpam($document)
    {
        $data = $this->_convertDocument($document);
        return $this->_akismet->isSpam($data);
    }

    public function submitSpam($document)
    {
        $data = $this->_convertDocument($document);
        $this->_akismet->submitSpam($data);
    }

    public function submitHam($document)
    {
        $data = $this->_convertDocument($document);
        $this->_akismet->submitHam($data);
    }

    /**
     * Converts a Fizzy_Spam_Document to Akismet request parameters
     *
     * Akismet paramters:
     * - blog: URL of the blog. If not provided, uses value returned by {@link getBlogUrl()}
     * - user_ip (required): IP address of comment submitter
     * - user_agent (required): User Agent used by comment submitter
     * - referrer: contents of HTTP_REFERER header
     * - permalink: location of the entry to which the comment was submitted
     * - comment_type: typically, one of 'blank', 'comment', 'trackback', or 'pingback', but may be any value
     * - comment_author: name submitted with the content
     * - comment_author_email: email submitted with the content
     * - comment_author_url: URL submitted with the content
     * - comment_content: actual content
     * 
     * @param Fizzy_Spam_Document $document
     * @return array
     */
    protected function _convertDocument(Fizzy_Spam_Document $document)
    {
        $params = array(
            'user_ip' => $document->getAuthorIp(),
            'user_agent' => $document->getUserAgent(),
            'referrer' => $document->getReferrer(),
            'comment_author' => $document->getAuthorName(),
            'comment_author_email' => $document->getAuthorEmail(),
            'comment_author_url' => $document->getAuthorWebsite(),
            'comment_content' => $document->getBody()
        );
        return $params;
    }
}
