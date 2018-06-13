<?php

namespace Arii\ACKBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * UjoAlarm
 *
 * @ORM\Table(name="ARII_ALARM")
 * @ORM\Entity(repositoryClass="Arii\ACKBundle\Entity\AlarmRepository")
 * 
 */
class Alarm
{
    public function __construct()
    {
        $this->alarm_time = new \DateTime();
        $this->state_time = new \DateTime();
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
    * @ORM\ManyToOne(targetEntity="Arii\ACKBundle\Entity\Alarm")
    * @ORM\JoinColumn(nullable=true)
    */
    private $alarm;
    
    /**
    * @ORM\ManyToOne(targetEntity="Arii\UserBundle\Entity\User")
    * @ORM\JoinColumn(nullable=true)
    */
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="alarm_type", type="string", length=32 )
     * 
     * @Serializer\Groups({"list"})
     */
    private $alarmType="MESSAGE";

    /**
     * @var integer
     *
     * @ORM\Column(name="alarm_time", type="datetime")
     * 
     * @Serializer\Groups({"list"})
     */
    private $alarm_time;
    
    /**
     * @var string
     *
     * @ORM\Column(name="response", type="string", length=255, nullable=true)
     */
    private $response;

    /**
     * @var integer
     *
     * @ORM\Column(name="state", type="string", length=32, nullable=true)
     * 
     * @Serializer\Groups({"list"})
     */
    private $state;

    /**
     * @var integer
     *
     * @ORM\Column(name="state_time", type="datetime", nullable=true)
     */
    private $state_time;


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
     * @return Alarm
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
     * Set alarmType
     *
     * @param string $alarmType
     * @return Alarm
     */
    public function setAlarmType($alarmType)
    {
        $this->alarmType = $alarmType;

        return $this;
    }

    /**
     * Get alarmType
     *
     * @return string 
     */
    public function getAlarmType()
    {
        return $this->alarmType;
    }

    /**
     * Set alarm_time
     *
     * @param \DateTime $alarmTime
     * @return Alarm
     */
    public function setAlarmTime($alarmTime)
    {
        $this->alarm_time = $alarmTime;

        return $this;
    }

    /**
     * Get alarm_time
     *
     * @return \DateTime 
     */
    public function getAlarmTime()
    {
        return $this->alarm_time;
    }

    /**
     * Set response
     *
     * @param string $response
     * @return Alarm
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Get response
     *
     * @return string 
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return Alarm
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
     * @return Alarm
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
     * Set alarm
     *
     * @param \Arii\ACKBundle\Entity\Alarm $alarm
     * @return Alarm
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

    /**
     * Set event
     *
     * @param \Arii\ACKBundle\Entity\Event $event
     * @return Alarm
     */
    public function setEvent(\Arii\ACKBundle\Entity\Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return \Arii\ACKBundle\Entity\Event 
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set network
     *
     * @param \Arii\ACKBundle\Entity\Network $network
     * @return Alarm
     */
    public function setNetwork(\Arii\ACKBundle\Entity\Network $network = null)
    {
        $this->network = $network;

        return $this;
    }

    /**
     * Get network
     *
     * @return \Arii\ACKBundle\Entity\Network 
     */
    public function getNetwork()
    {
        return $this->network;
    }

    /**
     * Set status
     *
     * @param \Arii\ACKBundle\Entity\Status $status
     * @return Alarm
     */
    public function setStatus(\Arii\ACKBundle\Entity\Status $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \Arii\ACKBundle\Entity\Status 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set user
     *
     * @param \Arii\UserBundle\Entity\User $user
     * @return Alarm
     */
    public function setUser(\Arii\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Arii\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
