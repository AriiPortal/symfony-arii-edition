<?php

namespace Arii\ACKBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cron
 *
 * @ORM\Table(name="ARII_EVENT")
 * @ORM\Entity(repositoryClass="Arii\ACKBundle\Entity\EventRepository")
 */
class Event
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64)
     */        
    private $name;
    
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
     * @ORM\Column(name="event", type="text", nullable=true )
     */        
    private $event;

     /**
     * @var string
     *
     * @ORM\Column(name="event_source", type="string", length=255, nullable=true )
     */        
    private $event_source;
    
     /**
     * @var string
     *
     * @ORM\Column(name="event_type", type="string", nullable=true )
     */        
    private $event_type;
    
    /**
     * @var datetime
     *
     * @ORM\Column(name="start_time", type="datetime", nullable=true )
     */        
    private $start_time;

    /**
     * @var datetime
     *
     * @ORM\Column(name="end_time", type="datetime", nullable=true )
     */        
    private $end_time;    

     /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=32, nullable=true)
     */        
    private $status;    

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=32, nullable=true)
     */        
    private $state;    
    
     /**
     * @var datetime
     *
     * @ORM\Column(name="state_time", type="datetime", length=32, nullable=true)
     */        
    private $state_time;    

    /**
     * @var string
     *
     * @ORM\Column(name="last_comment", type="text", nullable=true)
     */        
    private $last_comment;   
    
     /**
     * @var string
     *
     * @ORM\Column(name="comment", type="string", length=255, nullable=true)
     */        
    private $event_link;
    
    /**
     * @var datetime
     *
     * @ORM\Column(name="change_date", type="datetime", nullable=true )
     */        
    private $change_date;  
    
    /**
     * @var datetime
     *
     * @ORM\Column(name="change_start", type="datetime", nullable=true )
     */        
    private $change_start;  
    
    /**
     * @var datetime
     *
     * @ORM\Column(name="change_end", type="datetime", nullable=true )
     */        
    private $change_end;  

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
     * @return Event
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
     * @return Event
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
     * @return Event
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
     * Set event
     *
     * @param string $event
     * @return Event
     */
    public function setEvent($event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return string 
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set event_source
     *
     * @param string $eventSource
     * @return Event
     */
    public function setEventSource($eventSource)
    {
        $this->event_source = $eventSource;

        return $this;
    }

    /**
     * Get event_source
     *
     * @return string 
     */
    public function getEventSource()
    {
        return $this->event_source;
    }

    /**
     * Set event_type
     *
     * @param string $eventType
     * @return Event
     */
    public function setEventType($eventType)
    {
        $this->event_type = $eventType;

        return $this;
    }

    /**
     * Get event_type
     *
     * @return string 
     */
    public function getEventType()
    {
        return $this->event_type;
    }

    /**
     * Set start_time
     *
     * @param \DateTime $startTime
     * @return Event
     */
    public function setStartTime($startTime)
    {
        $this->start_time = $startTime;

        return $this;
    }

    /**
     * Get start_time
     *
     * @return \DateTime 
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * Set end_time
     *
     * @param \DateTime $endTime
     * @return Event
     */
    public function setEndTime($endTime)
    {
        $this->end_time = $endTime;

        return $this;
    }

    /**
     * Get end_time
     *
     * @return \DateTime 
     */
    public function getEndTime()
    {
        return $this->end_time;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Event
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
     * Set state
     *
     * @param string $state
     * @return Event
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
     * @return Event
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
     * Set last_comment
     *
     * @param string $lastComment
     * @return Event
     */
    public function setLastComment($lastComment)
    {
        $this->last_comment = $lastComment;

        return $this;
    }

    /**
     * Get last_comment
     *
     * @return string 
     */
    public function getLastComment()
    {
        return $this->last_comment;
    }

    /**
     * Set event_link
     *
     * @param string $eventLink
     * @return Event
     */
    public function setEventLink($eventLink)
    {
        $this->event_link = $eventLink;

        return $this;
    }

    /**
     * Get event_link
     *
     * @return string 
     */
    public function getEventLink()
    {
        return $this->event_link;
    }

    /**
     * Set change_date
     *
     * @param \DateTime $changeDate
     * @return Event
     */
    public function setChangeDate($changeDate)
    {
        $this->change_date = $changeDate;

        return $this;
    }

    /**
     * Get change_date
     *
     * @return \DateTime 
     */
    public function getChangeDate()
    {
        return $this->change_date;
    }

    /**
     * Set change_start
     *
     * @param \DateTime $changeStart
     * @return Event
     */
    public function setChangeStart($changeStart)
    {
        $this->change_start = $changeStart;

        return $this;
    }

    /**
     * Get change_start
     *
     * @return \DateTime 
     */
    public function getChangeStart()
    {
        return $this->change_start;
    }

    /**
     * Set change_end
     *
     * @param \DateTime $changeEnd
     * @return Event
     */
    public function setChangeEnd($changeEnd)
    {
        $this->change_end = $changeEnd;

        return $this;
    }

    /**
     * Get change_end
     *
     * @return \DateTime 
     */
    public function getChangeEnd()
    {
        return $this->change_end;
    }

    /**
     * Set alarm
     *
     * @param \Arii\ACKBundle\Entity\Alarm $alarm
     * @return Event
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
