<?php

namespace Arii\ACKBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * Object
 *
 * @ORM\Table(name="ARII_OBJECT")
 * @ORM\Entity(repositoryClass="Arii\ACKBundle\Entity\ObjectRepository")
 * 
 */
class Object
{
    public function __construct()
    {
    }
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     * @Serializer\Groups({"list","detail"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64)
     * @Assert\NotBlank(groups={"Create"})
     * 
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=64)
     * 
     * @Serializer\Groups({"list"})
     */
    private $title;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     * @Assert\NotBlank(groups={"Create"})
     * 
     * @Serializer\Groups({"detail"})
     */
    private $description;
    
    /**
     * @var string
     *
     * @ORM\Column(name="obj_type", type="string", length=24)
     * 
     */
    private $obj_type;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=24)
     * 
     */
    private $source;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="state", type="string", length=24)
     * 
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="state_comment", type="string", length=255)
     * 
     */
    private $state_comment;

    /**
     * @var datetime
     *
     * @ORM\Column(name="state_time", type="datetime")
     * 
     */
    private $state_time;

    /**
     * @var string
     *
     * @ORM\Column(name="state_user", type="string", length=64)
     * 
     */
    private $state_user;

    /**
     * @var datetime
     *
     * @ORM\Column(name="state_end", type="datetime")
     * 
     */
    private $state_end;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=12)
     * 
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="status_comment", type="string", length=255)
     * 
     */
    private $status_comment;

    /**
     * @var datetime
     *
     * @ORM\Column(name="status_time", type="datetime")
     * 
     */
    private $status_time;

    /**
     * @var string
     *
     * @ORM\Column(name="status_user", type="string", length=64)
     * 
     */
    private $status_user;

    /**
     * @var boolean
     *
     * @ORM\Column(name="ack", type="boolean")
     * 
     */
    private $ack;

    /**
     * @var string
     *
     * @ORM\Column(name="ack_comment", type="boolean")
     * 
     */
    private $ack_comment;

    /**
     * @var datetime
     *
     * @ORM\Column(name="ack_time", type="datetime" )
     * 
     */
    private $ack_time;

    /**
     * @var string
     *
     * @ORM\Column(name="ack_user", type="string", length=64)
     * 
     */
    private $ack_user;

    /**
     * @var datetime
     *
     * @ORM\Column(name="ack_end", type="datetime" )
     * 
     */
    private $ack_end;

    /**
     * @var boolean
     *
     * @ORM\Column(name="downtime", type="boolean")
     * 
     */
    private $downtime;

    /**
     * @var string
     *
     * @ORM\Column(name="downtime_comment", type="string", length=255)
     * 
     */
    private $downtime_comment;

    /**
     * @var datetime
     *
     * @ORM\Column(name="downtime_time", type="datetime")
     * 
     */
    private $downtime_time;

    /**
     * @var string
     *
     * @ORM\Column(name="downtime_user", type="string", length=64)
     * 
     */
    private $downtime_user;

    /**
     * @var datetime
     *
     * @ORM\Column(name="downtime_end", type="datetime")
     * 
     */
    private $downtime_end;
    
    /**
     * @var datetime
     *
     * @ORM\Column(name="updated", type="datetime")
     * 
     */
    private $updated;

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
     * @return Object
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
     * @return Object
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
     * @return Object
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
     * Set obj_type
     *
     * @param string $objType
     * @return Object
     */
    public function setObjType($objType)
    {
        $this->obj_type = $objType;

        return $this;
    }

    /**
     * Get obj_type
     *
     * @return string 
     */
    public function getObjType()
    {
        return $this->obj_type;
    }

    /**
     * Set source
     *
     * @param string $source
     * @return Object
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
     * Set state
     *
     * @param string $state
     * @return Object
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
     * Set state_comment
     *
     * @param string $stateComment
     * @return Object
     */
    public function setStateComment($stateComment)
    {
        $this->state_comment = $stateComment;

        return $this;
    }

    /**
     * Get state_comment
     *
     * @return string 
     */
    public function getStateComment()
    {
        return $this->state_comment;
    }

    /**
     * Set state_time
     *
     * @param \DateTime $stateTime
     * @return Object
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
     * Set state_user
     *
     * @param string $stateUser
     * @return Object
     */
    public function setStateUser($stateUser)
    {
        $this->state_user = $stateUser;

        return $this;
    }

    /**
     * Get state_user
     *
     * @return string 
     */
    public function getStateUser()
    {
        return $this->state_user;
    }

    /**
     * Set state_end
     *
     * @param \DateTime $stateEnd
     * @return Object
     */
    public function setStateEnd($stateEnd)
    {
        $this->state_end = $stateEnd;

        return $this;
    }

    /**
     * Get state_end
     *
     * @return \DateTime 
     */
    public function getStateEnd()
    {
        return $this->state_end;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Object
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
     * Set status_comment
     *
     * @param string $statusComment
     * @return Object
     */
    public function setStatusComment($statusComment)
    {
        $this->status_comment = $statusComment;

        return $this;
    }

    /**
     * Get status_comment
     *
     * @return string 
     */
    public function getStatusComment()
    {
        return $this->status_comment;
    }

    /**
     * Set status_time
     *
     * @param \DateTime $statusTime
     * @return Object
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
     * Set status_user
     *
     * @param string $statusUser
     * @return Object
     */
    public function setStatusUser($statusUser)
    {
        $this->status_user = $statusUser;

        return $this;
    }

    /**
     * Get status_user
     *
     * @return string 
     */
    public function getStatusUser()
    {
        return $this->status_user;
    }

    /**
     * Set ack
     *
     * @param boolean $ack
     * @return Object
     */
    public function setAck($ack)
    {
        $this->ack = $ack;

        return $this;
    }

    /**
     * Get ack
     *
     * @return boolean 
     */
    public function getAck()
    {
        return $this->ack;
    }

    /**
     * Set ack_comment
     *
     * @param string $ackComment
     * @return Object
     */
    public function setAckComment($ackComment)
    {
        $this->ack_comment = $ackComment;

        return $this;
    }

    /**
     * Get ack_comment
     *
     * @return string 
     */
    public function getAckComment()
    {
        return $this->ack_comment;
    }

    /**
     * Set ack_time
     *
     * @param \DateTime $ackTime
     * @return Object
     */
    public function setAckTime($ackTime)
    {
        $this->ack_time = $ackTime;

        return $this;
    }

    /**
     * Get ack_time
     *
     * @return \DateTime 
     */
    public function getAckTime()
    {
        return $this->ack_time;
    }

    /**
     * Set ack_user
     *
     * @param string $ackUser
     * @return Object
     */
    public function setAckUser($ackUser)
    {
        $this->ack_user = $ackUser;

        return $this;
    }

    /**
     * Get ack_user
     *
     * @return string 
     */
    public function getAckUser()
    {
        return $this->ack_user;
    }

    /**
     * Set ack_end
     *
     * @param \DateTime $ackEnd
     * @return Object
     */
    public function setAckEnd($ackEnd)
    {
        $this->ack_end = $ackEnd;

        return $this;
    }

    /**
     * Get ack_end
     *
     * @return \DateTime 
     */
    public function getAckEnd()
    {
        return $this->ack_end;
    }

    /**
     * Set downtime
     *
     * @param string $downtime
     * @return Object
     */
    public function setActive($downtime)
    {
        $this->downtime = $downtime;

        return $this;
    }

    /**
     * Get downtime
     *
     * @return string 
     */
    public function getActive()
    {
        return $this->downtime;
    }

    /**
     * Set downtime_comment
     *
     * @param string $downtimeComment
     * @return Object
     */
    public function setActiveComment($downtimeComment)
    {
        $this->downtime_comment = $downtimeComment;

        return $this;
    }

    /**
     * Get downtime_comment
     *
     * @return string 
     */
    public function getActiveComment()
    {
        return $this->downtime_comment;
    }

    /**
     * Set downtime_time
     *
     * @param \DateTime $downtimeTime
     * @return Object
     */
    public function setActiveTime($downtimeTime)
    {
        $this->downtime_time = $downtimeTime;

        return $this;
    }

    /**
     * Get downtime_time
     *
     * @return \DateTime 
     */
    public function getActiveTime()
    {
        return $this->downtime_time;
    }

    /**
     * Set downtime_user
     *
     * @param string $downtimeUser
     * @return Object
     */
    public function setActiveUser($downtimeUser)
    {
        $this->downtime_user = $downtimeUser;

        return $this;
    }

    /**
     * Get downtime_user
     *
     * @return string 
     */
    public function getActiveUser()
    {
        return $this->downtime_user;
    }

    /**
     * Set downtime_end
     *
     * @param \DateTime $downtimeEnd
     * @return Object
     */
    public function setActiveEnd($downtimeEnd)
    {
        $this->downtime_end = $downtimeEnd;

        return $this;
    }

    /**
     * Get downtime_end
     *
     * @return \DateTime 
     */
    public function getActiveEnd()
    {
        return $this->downtime_end;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Object
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
}
