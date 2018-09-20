<?php

namespace Arii\ACKBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cron
 *
 * @ORM\Table(name="ARII_ACTION")
 * @ORM\Entity(repositoryClass="Arii\ACKBundle\Entity\ActionRepository")
 */
class Action
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
     * @ORM\Column(name="description", type="text", nullable=true)
     */        
    private $description;
    
    /**
     * @ORM\ManyToOne(targetEntity="Arii\ACKBundle\Entity\Event")
     * @ORM\JoinColumn(nullable=true)
     **/
    private $event;
    
    /**
     * @var datetime
     *
     * @ORM\Column(name="date_time", type="datetime", length=3, nullable=true )
     */        
    private $date_time;

     /**
     * @var string
     *
     * @ORM\Column(name="user", type="string", length=255, nullable=true)
     */        
    private $user;
    
     /**
     * @var string
     *
     * @ORM\Column(name="num", type="integer", nullable=true)
     */        
    private $num;
    
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
     * @return Action
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
     * @return Action
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
     * @return Action
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
     * Set date_time
     *
     * @param \DateTime $dateTime
     * @return Action
     */
    public function setDateTime($dateTime)
    {
        $this->date_time = $dateTime;

        return $this;
    }

    /**
     * Get date_time
     *
     * @return \DateTime 
     */
    public function getDateTime()
    {
        return $this->date_time;
    }

    /**
     * Set event
     *
     * @param \Arii\ACKBundle\Entity\Event $event
     * @return Action
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
     * Set user
     *
     * @param string $user
     * @return Action
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
     * Set num
     *
     * @param integer $num
     * @return Action
     */
    public function setNum($num)
    {
        $this->num = $num;

        return $this;
    }

    /**
     * Get num
     *
     * @return integer 
     */
    public function getNum()
    {
        return $this->num;
    }
}
