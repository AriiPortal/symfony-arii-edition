<?php

namespace Arii\MFTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Connections
 *
 * @ORM\Table(name="MFT_TRANSFERS")
 * @ORM\Entity(readOnly=true,repositoryClass="Arii\MFTBundle\Entity\TransfersRepository")
 */
class Transfers
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
    * @ORM\ManyToOne(targetEntity="Arii\MFTBundle\Entity\Partners")
    * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
    */
    private $partner;

    /**
    * @ORM\ManyToOne(targetEntity="Arii\MFTBundle\Entity\Schedules")
    * @ORM\JoinColumn(nullable=true)
    */
    private $schedule;
    
    /**
    * @ORM\ManyToOne(targetEntity="Arii\MFTBundle\Entity\Summary")
    * @ORM\JoinColumn(nullable=true)
    */
    private $summary;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64)
     */        
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=512, nullable=true)
     */        
    private $title;
    
    /**
     * @var string
     *
     * @ORM\Column(name="env", type="string", length=16, nullable=true)
     */        
    private $env;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=512, nullable=true)
     */        
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="steps", type="integer", nullable=true)
     */        
    private $steps;
    
    /**
     * @var string
     *
     * @ORM\Column(name="step_start", type="string", length=32, nullable=true)
     */        
    private $step_start;
    
    /**
     * @var string
     *
     * @ORM\Column(name="step_end", type="string", length=32, nullable=true)
     */        
    private $step_end;

    /**
     * @var datetime
     *
     * @ORM\Column(name="change_time", type="datetime", nullable=true )
     */        
    private $change_time;

    /**
     * @var datetime
     *
     * @ORM\Column(name="change_user",  type="string", length=64, nullable=true)
     */        
    private $change_user;
    

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
     * @return Transfers
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
     * @return Transfers
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
     * Set env
     *
     * @param string $env
     * @return Transfers
     */
    public function setEnv($env)
    {
        $this->env = $env;

        return $this;
    }

    /**
     * Get env
     *
     * @return string 
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Transfers
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
     * Set step_start
     *
     * @param integer $stepStart
     * @return Transfers
     */
    public function setStepStart($stepStart)
    {
        $this->step_start = $stepStart;

        return $this;
    }

    /**
     * Get step_start
     *
     * @return integer 
     */
    public function getStepStart()
    {
        return $this->step_start;
    }

    /**
     * Set step_end
     *
     * @param integer $stepEnd
     * @return Transfers
     */
    public function setStepEnd($stepEnd)
    {
        $this->step_end = $stepEnd;

        return $this;
    }

    /**
     * Get step_end
     *
     * @return integer 
     */
    public function getStepEnd()
    {
        return $this->step_end;
    }

    /**
     * Set change_time
     *
     * @param \DateTime $changeTime
     * @return Transfers
     */
    public function setChangeTime($changeTime)
    {
        $this->change_time = $changeTime;

        return $this;
    }

    /**
     * Get change_time
     *
     * @return \DateTime 
     */
    public function getChangeTime()
    {
        return $this->change_time;
    }

    /**
     * Set change_user
     *
     * @param string $changeUser
     * @return Transfers
     */
    public function setChangeUser($changeUser)
    {
        $this->change_user = $changeUser;

        return $this;
    }

    /**
     * Get change_user
     *
     * @return string 
     */
    public function getChangeUser()
    {
        return $this->change_user;
    }

    /**
     * Set partner
     *
     * @param \Arii\MFTBundle\Entity\Partners $partner
     * @return Transfers
     */
    public function setPartner(\Arii\MFTBundle\Entity\Partners $partner = null)
    {
        $this->partner = $partner;

        return $this;
    }

    /**
     * Get partner
     *
     * @return \Arii\MFTBundle\Entity\Partners 
     */
    public function getPartner()
    {
        return $this->partner;
    }

    /**
     * Set steps
     *
     * @param integer $steps
     * @return Transfers
     */
    public function setSteps($steps)
    {
        $this->steps = $steps;

        return $this;
    }

    /**
     * Get steps
     *
     * @return integer 
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     * Set schedule
     *
     * @param \Arii\MFTBundle\Entity\Schedules $schedule
     * @return Transfers
     */
    public function setSchedule(\Arii\MFTBundle\Entity\Schedules $schedule = null)
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * Get schedule
     *
     * @return \Arii\MFTBundle\Entity\Schedules 
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * Set summary
     *
     * @param \Arii\MFTBundle\Entity\Summary $summary
     * @return Transfers
     */
    public function setSummary(\Arii\MFTBundle\Entity\Summary $summary = null)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return \Arii\MFTBundle\Entity\Summary 
     */
    public function getSummary()
    {
        return $this->summary;
    }
}
