<?php

namespace Arii\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ErrorLog
 *
 * @ORM\Table(name="ARII_ERROR_LOG")
 * @ORM\Entity(repositoryClass="Arii\CoreBundle\Entity\ErrorLogRepository")
 */
class ErrorLog
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
     * @ORM\Column(name="ip", type="string", length=64)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="module", type="string", length=30,nullable=true)
     */
    private $module;

    /**
     * @var string
     * 
     * @ORM\Column(name="message", type="text",nullable=true)
     *      
     */
    private $message;

    /**
     * @var integer
     * 
     * @ORM\Column(name="code",type="integer", length=3,nullable=true)
     *      
     */
    private $code;
    
    /**
     * @var string
     * 
     * @ORM\Column(name="file_name",type="string", length=255,nullable=true)
     *      
     */
    private $file_name;
    
    /**
     * @var integer
     * 
     * @ORM\Column(name="line",type="integer",length=11,nullable=true)
     *      
     */
    private $line;
    
    /**
     * @var text
     * 
     * @ORM\Column(name="trace",type="text", length=255,nullable=true)
     *      
     */
    private $trace;

    /**
     * Set username
     *
     * @param string $username
     * @return ErrorLog
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
     * @return ErrorLog
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
     * @return ErrorLog
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
     * Set module
     *
     * @param string $module
     * @return ErrorLog
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

    /**
     * Set message
     *
     * @param string $message
     * @return ErrorLog
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
     * Set code
     *
     * @param integer $code
     * @return ErrorLog
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return integer 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set file_name
     *
     * @param string $fileName
     * @return ErrorLog
     */
    public function setFileName($fileName)
    {
        $this->file_name = $fileName;

        return $this;
    }

    /**
     * Get file_name
     *
     * @return string 
     */
    public function getFileName()
    {
        return $this->file_name;
    }

    /**
     * Set line
     *
     * @param integer $line
     * @return ErrorLog
     */
    public function setLine($line)
    {
        $this->line = $line;

        return $this;
    }

    /**
     * Get line
     *
     * @return integer 
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * Set trace
     *
     * @param string $trace
     * @return ErrorLog
     */
    public function setTrace($trace)
    {
        $this->trace = $trace;

        return $this;
    }

    /**
     * Get trace
     *
     * @return string 
     */
    public function getTrace()
    {
        return $this->trace;
    }
}
