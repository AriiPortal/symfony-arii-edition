<?php

namespace Arii\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Audit
 *
 * @ORM\Table(name="ARII_AUDIT")
 * @ORM\Entity(repositoryClass="Arii\CoreBundle\Entity\AuditRepository")
 */
class Audit
{
    /**
     * @var string
     * 
     * @ORM\Column(name="username",type="string", length=50)
     *      
     */
    private $username;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="logtime", type="datetime")
     */
    private $logtime;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=15)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", length=255)
     */
    private $action;

    /**
     * @var text
     *
     * @ORM\Column(name="message", type="text",nullable=true)
     */
    private $message;
    
    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=12)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="module", type="string", length=10)
     */
    private $module;

    /**
     * Set username
     *
     * @param string $username
     * @return Audit
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set logtime
     *
     * @param \DateTime $logtime
     * @return Audit
     */
    public function setLogtime($logtime)
    {
        $this->logtime = $logtime;

        return $this;
    }

    /**
     * Get logtime
     *
     * @return \DateTime 
     */
    public function getLogtime()
    {
        return $this->logtime;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return Audit
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set action
     *
     * @param string $action
     * @return Audit
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Audit
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Audit
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set module
     *
     * @param string $module
     * @return Audit
     */
    public function setModule($module)
    {
        $this->module = $module;

        return $this;
    }

    /**
     * Get module
     *
     * @return string 
     */
    public function getModule()
    {
        return $this->module;
    }
}
