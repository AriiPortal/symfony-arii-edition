<?php

namespace Arii\ACKBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Service
 * Etat de l'infrastructure
 * 
 * @ORM\Table(name="ARII_SERVICE")
 * @ORM\Entity(repositoryClass="Arii\ACKBundle\Entity\ServiceRepository")
 * 
 */
class Service
{
    public function __construct()
    {
        $this->stateTime = new \DateTime();
    }
    
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
     * @ORM\Column(name="title", type="string", length=64)
     */
    private $title;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

// a quoi sert le status ?
     /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=32, nullable=true)
     */        
    private $status;    

     /**
     * @var datetime
     *
     * @ORM\Column(name="status_time", type="datetime", length=32, nullable=true)
     */        
    private $status_time;    
    
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
     * @ORM\Column(name="state_information", type="string", length=255, nullable=true)
     */
    private $state_information;
    
    /**
     * @var string
     *
     * @ORM\Column(name="acknowledgement_type", type="string", length=16, nullable=true)
     */
    private $acknowledgement_type;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="acknowledged", type="boolean")
     */
    private $acknowledged=0;

    /**
     * @var integer
     *
     * @ORM\Column(name="downtimes", type="string", length=255, nullable=true)
     */
    private $downtimes;
        
    /**
     * @var string
     *
     * @ORM\Column(name="downtime_info", type="string", length=255, nullable=true)
     */
    private $downtimes_info;

    /**
     * @var string
     *
     * @ORM\Column(name="downtime_user", type="string", length=32, nullable=true)
     */
    private $downtimes_user;
    
    /**
     * @var datetime
     *
     * @ORM\Column(name="last_state_change", type="datetime", nullable=true)
     */
    private $last_state_change;

    /**
     * @var datetime
     *
     * @ORM\Column(name="last_time_down", type="datetime", nullable=true)
     */
    private $last_time_down;
    
    /**
     * @var datetime
     *
     * @ORM\Column(name="last_time_unreachable", type="datetime", nullable=true)
     */
    private $last_time_unreachable;

    /**
     * @var datetime
     *
     * @ORM\Column(name="last_time_up", type="datetime", nullable=true)
     */
    private $last_time_up;

    /**
     * @var float
     *
     * @ORM\Column(name="latency", type="float", nullable=true)
     */
    private $latency;

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
     * @var string
     *
     * @ORM\Column(name="host_name", type="string", length=255, nullable=true )
     */        
    private $host_name;
    
    /**
    * @ORM\ManyToOne(targetEntity="Arii\ACKBundle\Entity\Host",cascade={"persist"})
    * @ORM\JoinColumn(nullable=true)
    */
    private $host;
    
    /**
    * @ORM\OneToOne(targetEntity="Arii\ACKBundle\Entity\Probe")
    * @ORM\JoinColumn(nullable=true)
    */
    private $probe;

    /**
     * @var boolean
     *
     * @ORM\Column(name="synchronised", type="boolean")
     */
    private $synchronised=1;

    
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
     * @return Service
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
     * @return Service
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
     * @return Service
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
     * @return Service
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
     * Set status_time
     *
     * @param \DateTime $statusTime
     * @return Service
     */
    public function setStatusTime($statusTime)
    {
        $this->status_time = $statusTime;

        return $this;
    }

    /**
     * Get status_time
     *
     * @return \DateTime 
     */
    public function getStatusTime()
    {
        return $this->status_time;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return Service
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
     * @return Service
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
     * Set state_information
     *
     * @param string $stateInformation
     * @return Service
     */
    public function setStateInformation($stateInformation)
    {
        $this->state_information = $stateInformation;

        return $this;
    }

    /**
     * Get state_information
     *
     * @return string 
     */
    public function getStateInformation()
    {
        return $this->state_information;
    }

    /**
     * Set acknowledgement_type
     *
     * @param string $acknowledgementType
     * @return Service
     */
    public function setAcknowledgementType($acknowledgementType)
    {
        $this->acknowledgement_type = $acknowledgementType;

        return $this;
    }

    /**
     * Get acknowledgement_type
     *
     * @return string 
     */
    public function getAcknowledgementType()
    {
        return $this->acknowledgement_type;
    }

    /**
     * Set acknowledged
     *
     * @param boolean $acknowledged
     * @return Service
     */
    public function setAcknowledged($acknowledged)
    {
        $this->acknowledged = $acknowledged;

        return $this;
    }

    /**
     * Get acknowledged
     *
     * @return boolean 
     */
    public function getAcknowledged()
    {
        return $this->acknowledged;
    }

    /**
     * Set downtimes
     *
     * @param string $downtimes
     * @return Service
     */
    public function setDowntimes($downtimes)
    {
        $this->downtimes = $downtimes;

        return $this;
    }

    /**
     * Get downtimes
     *
     * @return string 
     */
    public function getDowntimes()
    {
        return $this->downtimes;
    }

    /**
     * Set downtimes_info
     *
     * @param string $downtimesInfo
     * @return Service
     */
    public function setDowntimesInfo($downtimesInfo)
    {
        $this->downtimes_info = $downtimesInfo;

        return $this;
    }

    /**
     * Get downtimes_info
     *
     * @return string 
     */
    public function getDowntimesInfo()
    {
        return $this->downtimes_info;
    }

    /**
     * Set downtimes_user
     *
     * @param string $downtimesUser
     * @return Service
     */
    public function setDowntimesUser($downtimesUser)
    {
        $this->downtimes_user = $downtimesUser;

        return $this;
    }

    /**
     * Get downtimes_user
     *
     * @return string 
     */
    public function getDowntimesUser()
    {
        return $this->downtimes_user;
    }

    /**
     * Set last_state_change
     *
     * @param \DateTime $lastStateChange
     * @return Service
     */
    public function setLastStateChange($lastStateChange)
    {
        $this->last_state_change = $lastStateChange;

        return $this;
    }

    /**
     * Get last_state_change
     *
     * @return \DateTime 
     */
    public function getLastStateChange()
    {
        return $this->last_state_change;
    }

    /**
     * Set last_time_down
     *
     * @param \DateTime $lastTimeDown
     * @return Service
     */
    public function setLastTimeDown($lastTimeDown)
    {
        $this->last_time_down = $lastTimeDown;

        return $this;
    }

    /**
     * Get last_time_down
     *
     * @return \DateTime 
     */
    public function getLastTimeDown()
    {
        return $this->last_time_down;
    }

    /**
     * Set last_time_unreachable
     *
     * @param \DateTime $lastTimeUnreachable
     * @return Service
     */
    public function setLastTimeUnreachable($lastTimeUnreachable)
    {
        $this->last_time_unreachable = $lastTimeUnreachable;

        return $this;
    }

    /**
     * Get last_time_unreachable
     *
     * @return \DateTime 
     */
    public function getLastTimeUnreachable()
    {
        return $this->last_time_unreachable;
    }

    /**
     * Set last_time_up
     *
     * @param \DateTime $lastTimeUp
     * @return Service
     */
    public function setLastTimeUp($lastTimeUp)
    {
        $this->last_time_up = $lastTimeUp;

        return $this;
    }

    /**
     * Get last_time_up
     *
     * @return \DateTime 
     */
    public function getLastTimeUp()
    {
        return $this->last_time_up;
    }

    /**
     * Set latency
     *
     * @param float $latency
     * @return Service
     */
    public function setLatency($latency)
    {
        $this->latency = $latency;

        return $this;
    }

    /**
     * Get latency
     *
     * @return float 
     */
    public function getLatency()
    {
        return $this->latency;
    }

    /**
     * Set event_source
     *
     * @param string $eventSource
     * @return Service
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
     * @return Service
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
     * Set host_name
     *
     * @param string $hostName
     * @return Service
     */
    public function setHostName($hostName)
    {
        $this->host_name = $hostName;

        return $this;
    }

    /**
     * Get host_name
     *
     * @return string 
     */
    public function getHostName()
    {
        return $this->host_name;
    }

    /**
     * Set host
     *
     * @param \Arii\ACKBundle\Entity\Host $host
     * @return Service
     */
    public function setHost(\Arii\ACKBundle\Entity\Host $host = null)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get host
     *
     * @return \Arii\ACKBundle\Entity\Host 
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set probe
     *
     * @param \Arii\ACKBundle\Entity\Probe $probe
     * @return Service
     */
    public function setProbe(\Arii\ACKBundle\Entity\Probe $probe = null)
    {
        $this->probe = $probe;

        return $this;
    }

    /**
     * Get probe
     *
     * @return \Arii\ACKBundle\Entity\Probe 
     */
    public function getProbe()
    {
        return $this->probe;
    }

    /**
     * Set synchronised
     *
     * @param boolean $synchronised
     * @return Service
     */
    public function setSynchronised($synchronised)
    {
        $this->synchronised = $synchronised;

        return $this;
    }

    /**
     * Get synchronised
     *
     * @return boolean 
     */
    public function getSynchronised()
    {
        return $this->synchronised;
    }
}
