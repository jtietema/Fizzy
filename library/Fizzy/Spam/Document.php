<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Document
 *
 * @author jeroen
 */
class Fizzy_Spam_Document
{
    protected $_body;
    protected $_authorName;
    protected $_authorEmail;
    protected $_authorWebsite;
    protected $_authorIp;
    protected $_userAgent;
    protected $_referrer;

    public function __construct(
            $body,
            $authorName,
            $authorEmail,
            $authorWebsite,
            $authorIp,
            $userAgent,
            $referrer)
    {
        $this->_body = $body;
        $this->_authorName = $authorName;
        $this->_authorEmail = $authorEmail;
        $this->_authorWebsite = $authorWebsite;
        $this->_authorIp = $authorIp;
        $this->_userAgent = $userAgent;
        $this->_referrer = $referrer;
    }

    public function getBody()
    {
        return $this->_body;
    }

    public function setBody($body)
    {
        $this->_body = $body;
        return $this;
    }

    public function getAuthorName()
    {
        return $this->_authorName;
    }

    public function setAuthorName($name)
    {
        $this->_authorName = $name;
        return $this;
    }

    public function getAuthorEmail()
    {
        return $this->_authorEmail;
    }

    public function setAuthorEmail($email)
    {
        $this->_authorEmail = $email;
        return $this;
    }

    public function getAuthorWebsite()
    {
        return $this->_authorWebsite;
    }

    public function setAuthorWebsite($website)
    {
        $this->_authorWebsite = $website;
        return $this;
    }

    public function getAuthorIp()
    {
        return $this->_authorIp;
    }

    public function setAuthorIp($ip)
    {
        $this->_authorIp = $ip;
        return $this;
    }

    public function getUserAgent()
    {
        return $this->_userAgent;
    }

    public function setUserAgent($userAgent)
    {
        $this->_userAgent = $userAgent;
        return $this;
    }

    public function getReferrer()
    {
        return $this->_referrer;
    }

    public function setReferrer($referrer)
    {
        $this->_referrer = $referrer;
        return $this;
    }
}
