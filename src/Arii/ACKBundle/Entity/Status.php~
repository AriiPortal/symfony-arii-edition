<?php

namespace Arii\ACKBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * Status
 * Etat des systemes
 * 
 * @ORM\Table(name="ARII_STATUS")
 * @ORM\Entity(repositoryClass="Arii\ACKBundle\Entity\StatusRepository")
 * 
 */
class Status
{
    public function __construct()
    {
        $this->alarmTime = new \DateTime();
        $this->stateTime = new \DateTime();
    }
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     */
    private $id;
   
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     * 
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * 
     */
    private $title;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     * @Assert\NotBlank(groups={"Create"})
     * 
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="string", length=32 )
     * 
     */
    private $type;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="source", type="string", length=32 )
     * 
     */
    private $source;

    /**
     * @var integer
     *
     * @ORM\Column(name="instance", type="string", length=32 )
     * 
     */
    private $instance;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="user", type="string", length=32, nullable=true )
     * 
     */
    private $user;    

    /**
     * @var integer
     *
     * @ORM\Column(name="exit_code", type="integer" )
     * 
     */
    private $exit_code=0;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="run_try", type="float" )
     * 
     */
    private $run_try=0;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="string", length=32, nullable=true)
     * 
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="status_time", type="datetime", nullable=true)
     * 
     */
    private $status_time;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="last_start", type="datetime", nullable=true)
     * 
     */
    private $last_start;

    /**
     * @var integer
     *
     * @ORM\Column(name="last_end", type="datetime", nullable=true)
     * 
     */
    private $last_end;

    /**
     * @var integer
     *
     * @ORM\Column(name="updated", type="datetime")
     * 
     */
    private $updated;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="message", type="string", length=255, nullable=true)
     * 
     */
    private $message;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="string", length=32, nullable=true)
     * 
     */
    private $state;

    /**
     * @var integer
     *
     * @ORM\Column(name="state_time", type="datetime", nullable=true)
     * 
     */
    private $state_time;

    /**
     * @var integer
     *
     * @ORM\Column(name="job_log", type="text" )
     * 
     */
    private $job_log;

    /**
    * @ORM\ManyToOne(targetEntity="Arii\ACKBundle\Entity\Alarm")
    * @ORM\JoinColumn(nullable=true)
    */
    private $alarm;
    
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
     * Set name
     *
     * @param string $name
     * @return Status
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Status
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Status
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Status
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set source
     *
     * @param string $source
     * @return Status
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return string 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set user
     *
     * @param string $user
     * @return Status
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set exit_code
     *
     * @param integer $exitCode
     * @return Status
     */
    public function setExitCode($exitCode)
    {
        $this->exit_code = $exitCode;

        return $this;
    }

    /**
     * Get exit_code
     *
     * @return integer 
     */
    public function getExitCode()
    {
        return $this->exit_code;
    }

    /**
     * Set run_try
     *
     * @param float $runTry
     * @return Status
     */
    public function setRunTry($runTry)
    {
        $this->run_try = $runTry;

        return $this;
    }

    /**
     * Get run_try
     *
     * @return float 
     */
    public function getRunTry()
    {
        return $this->run_try;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Status
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
     * Set last_start
     *
     * @param \DateTime $lastStart
     * @return Status
     */
    public function setLastStart($lastStart)
    {
        $this->last_start = $lastStart;

        return $this;
    }

    /**
     * Get last_start
     *
     * @return \DateTime 
     */
    public function getLastStart()
    {
        return $this->last_start;
    }

    /**
     * Set last_end
     *
     * @param \DateTime $lastEnd
     * @return Status
     */
    public function setLastEnd($lastEnd)
    {
        $this->last_end = $lastEnd;

        return $this;
    }

    /**
     * Get last_end
     *
     * @return \DateTime 
     */
    public function getLastEnd()
    {
        return $this->last_end;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Status
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set instance
     *
     * @param string $instance
     * @return Status
     */
    public function setInstance($instance)
    {
        $this->instance = $instance;

        return $this;
    }

    /**
     * Get instance
     *
     * @return string 
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Status
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
     * Set state
     *
     * @param string $state
     * @return Status
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set state_time
     *
     * @param \DateTime $stateTime
     * @return Status
     */
    public function setStateTime($stateTime)
    {
        $this->state_time = $stateTime;

        return $this;
    }

    /**
     * Get state_time
     *
     * @return \DateTime 
     */
    public function getStateTime()
    {
        return $this->state_time;
    }

    /**
     * Set status_time
     *
     * @param string $statusTime
     * @return Status
     */
    public function setStatusTime($statusTime)
    {
        $this->status_time = $statusTime;

        return $this;
    }

    /**
     * Get status_time
     *
     * @return string 
     */
    public function getStatusTime()
    {
        return $this->status_time;
    }

    /**
     * Set log
     *
     * @param string $log
     * @return Status
     */
    public function setLog($log)
    {
        $this->log = $log;

        return $this;
    }

    /**
     * Get log
     *
     * @return string 
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Set job_log
     *
     * @param string $jobLog
     * @return Status
     */
    public function setJobLog($jobLog)
    {
        $this->job_log = $jobLog;

        return $this;
    }

    /**
     * Get job_log
     *
     * @return string 
     */
    public function getJobLog()
    {
        return $this->job_log;
    }

    /**
     * Set alarm
     *
     * @param \Arii\ACKBundle\Entity\Alarm $alarm
     * @return Status
     */
    public function setAlarm(\Arii\ACKBundle\Entity\Alarm $alarm = null)
    {
        $this->alarm = $alarm;

        return $this;
    }

    /**
     * Get alarm
     *
     * @return \Arii\ACKBundle\Entity\Alarm 
     */
    public function getAlarm()
    {
        return $this->alarm;
    }
}
