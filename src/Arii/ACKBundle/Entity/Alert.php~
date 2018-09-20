<?php

namespace Arii\ACKBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Alert
 *
 * @ORM\Table(name="ARII_ALERT")
 * @ORM\Entity(repositoryClass="Arii\ACKBundle\Entity\AlertRepository")
 */
class Alert
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    * @ORM\ManyToOne(targetEntity="Arii\CoreBundle\Entity\Application")
    * @ORM\JoinColumn(nullable=true)
    */
    private $application;
    
    /**
    * @ORM\ManyToOne(targetEntity="Arii\CoreBundle\Entity\Note")
    * @ORM\JoinColumn(nullable=true)
    */
    private $note;

     /**
     * @var string
     *
     * @ORM\Column(name="origin", type="string", length=64)
     */        
    private $origin;
    
     /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64)
     */        
    private $name;
    
     /**
     * @var string
     *
     * @ORM\Column(name="pattern", type="string", length=128)
     */        
    private $pattern;
    
     /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */        
    private $title;

     /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */        
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=128, nullable=true)
     */        
    private $status;
    
    /**
     * @var string
     *
     * @ORM\Column(name="time_slot", type="string", length=64, nullable=true)
     */        
    private $time_slot;
    
    /**
     * @var string
     *
     * @ORM\Column(name="exit_code", type="string", length=255, nullable=true)
     */        
    private $exit_codes;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=255, nullable=true)
     */        
    private $message;
    
    /**
     * @var string
     *
     * @ORM\Column(name="msg_type", type="string", length=32, nullable=true)
     */        
    private $msg_type;

    /**
     * @var string
     *
     * @ORM\Column(name="msg_from", type="string", length=32, nullable=true)
     */        
    private $msg_from;
    
    /**
     * @var string
     *
     * @ORM\Column(name="msg_to", type="string", length=255, nullable=true)
     */        
    private $msg_to;
    
    /**
     * @var string
     *
     * @ORM\Column(name="msg_cc", type="string", length=255, nullable=true)
     */        
    private $msg_cc;

    /**
     * @var string
     *
     * @ORM\Column(name="to_do", type="text", nullable=true)
     */        
    private $to_do;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="text", nullable=true)
     */        
    private $action;

    // Activer l'alarme
    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean" )
     */        
    private $active=1;

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
     * @return Alerts
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
     * Set pattern
     *
     * @param string $pattern
     * @return Alerts
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;

        return $this;
    }

    /**
     * Get pattern
     *
     * @return string 
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Alerts
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
     * @return Alerts
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
     * Set status
     *
     * @param string $status
     * @return Alerts
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
     * Set time_slot
     *
     * @param string $timeSlot
     * @return Alerts
     */
    public function setTimeSlot($timeSlot)
    {
        $this->time_slot = $timeSlot;

        return $this;
    }

    /**
     * Get time_slot
     *
     * @return string 
     */
    public function getTimeSlot()
    {
        return $this->time_slot;
    }

    /**
     * Set exit_codes
     *
     * @param string $exitCodes
     * @return Alerts
     */
    public function setExitCodes($exitCodes)
    {
        $this->exit_codes = $exitCodes;

        return $this;
    }

    /**
     * Get exit_codes
     *
     * @return string 
     */
    public function getExitCodes()
    {
        return $this->exit_codes;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return Alerts
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
     * Set msg_type
     *
     * @param string $msgType
     * @return Alerts
     */
    public function setMsgType($msgType)
    {
        $this->msg_type = $msgType;

        return $this;
    }

    /**
     * Get msg_type
     *
     * @return string 
     */
    public function getMsgType()
    {
        return $this->msg_type;
    }

    /**
     * Set msg_from
     *
     * @param string $msgFrom
     * @return Alerts
     */
    public function setMsgFrom($msgFrom)
    {
        $this->msg_from = $msgFrom;

        return $this;
    }

    /**
     * Get msg_from
     *
     * @return string 
     */
    public function getMsgFrom()
    {
        return $this->msg_from;
    }

    /**
     * Set msg_to
     *
     * @param string $msgTo
     * @return Alerts
     */
    public function setMsgTo($msgTo)
    {
        $this->msg_to = $msgTo;

        return $this;
    }

    /**
     * Get msg_to
     *
     * @return string 
     */
    public function getMsgTo()
    {
        return $this->msg_to;
    }

    /**
     * Set msg_cc
     *
     * @param string $msgCc
     * @return Alerts
     */
    public function setMsgCc($msgCc)
    {
        $this->msg_cc = $msgCc;

        return $this;
    }

    /**
     * Get msg_cc
     *
     * @return string 
     */
    public function getMsgCc()
    {
        return $this->msg_cc;
    }

    /**
     * Set active
     *
     * @param boolean $active
     * @return Alerts
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set note
     *
     * @param \Arii\CoreBundle\Entity\Note $note
     * @return Alerts
     */
    public function setNote(\Arii\CoreBundle\Entity\Note $note = null)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return \Arii\CoreBundle\Entity\Note 
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set to_do
     *
     * @param string $toDo
     * @return Alerts
     */
    public function setToDo($toDo)
    {
        $this->to_do = $toDo;

        return $this;
    }

    /**
     * Get to_do
     *
     * @return string 
     */
    public function getToDo()
    {
        return $this->to_do;
    }

    /**
     * Set action
     *
     * @param string $action
     * @return Alerts
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
     * Set origin
     *
     * @param string $origin
     * @return Alerts
     */
    public function setOrigin($origin)
    {
        $this->origin = $origin;

        return $this;
    }

    /**
     * Get origin
     *
     * @return string 
     */
    public function getOrigin()
    {
        return $this->origin;
    }

    /**
     * Set application
     *
     * @param \Arii\CoreBundle\Entity\Application $application
     * @return Alerts
     */
    public function setApplication(\Arii\CoreBundle\Entity\Application $application = null)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get application
     *
     * @return \Arii\CoreBundle\Entity\Application 
     */
    public function getApplication()
    {
        return $this->application;
    }
}
