<?php

namespace Arii\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Filter
 *
 * @ORM\Table(name="ARII_FILTER")
 * @ORM\Entity(repositoryClass="Arii\CoreBundle\Entity\FilterRepository")
 */
class Filter
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
     * @ORM\ManyToOne(targetEntity="Arii\UserBundle\Entity\User")
     */
    private $owner;
     
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
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
     * @ORM\Column(name="env", type="string", length=8, nullable=true)
     */
    private $env;

    /**
     * @var string
     *
     * @ORM\Column(name="spooler", type="string", length=64, nullable=true)
     */
    private $spooler;

    /**
     * @var string
     *
     * @ORM\Column(name="job_name", type="string", length=255, nullable=true)
     */
    private $job_name;

    /**
     * @var string
     *
     * @ORM\Column(name="job_chain", type="string", length=255, nullable=true)
     */
    private $job_chain;

    /**
     * @var string
     *
     * @ORM\Column(name="folder", type="string", length=255, nullable=true)
     */
    private $folder;

    /**
     * @var string
     *
     * @ORM\Column(name="job_group", type="string", length=255, nullable=true)
     */
    private $job_group;

    /**
     * @var string
     *
     * @ORM\Column(name="trigger_job", type="string", length=255, nullable=true)
     */
    private $trigger;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=128, nullable=true)
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="filter_type", type="integer" )
     */
    private $filter_type=0;

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
     * @return Filter
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
     * Set description
     *
     * @param string $description
     * @return Filter
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
     * Set title
     *
     * @param string $title
     * @return Filter
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
     * @return Filter
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
     * Set spooler
     *
     * @param string $spooler
     * @return Filter
     */
    public function setSpooler($spooler)
    {
        $this->spooler = $spooler;

        return $this;
    }

    /**
     * Get spooler
     *
     * @return string 
     */
    public function getSpooler()
    {
        return $this->spooler;
    }

    /**
     * Set job_name
     *
     * @param string $jobName
     * @return Filter
     */
    public function setJobName($jobName)
    {
        $this->job_name = $jobName;

        return $this;
    }

    /**
     * Get job_name
     *
     * @return string 
     */
    public function getJobName()
    {
        return $this->job_name;
    }

    /**
     * Set folder
     *
     * @param string $folder
     * @return Filter
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * Get folder
     *
     * @return string 
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * Set job_chain
     *
     * @param string $jobChain
     * @return Filter
     */
    public function setJobChain($jobChain)
    {
        $this->job_chain = $jobChain;

        return $this;
    }

    /**
     * Get job_chain
     *
     * @return string 
     */
    public function getJobChain()
    {
        return $this->job_chain;
    }

    /**
     * Set job_group
     *
     * @param string $jobGroup
     * @return Filter
     */
    public function setJobGroup($jobGroup)
    {
        $this->job_group = $jobGroup;

        return $this;
    }

    /**
     * Get job_group
     *
     * @return string 
     */
    public function getJobGroup()
    {
        return $this->job_group;
    }

    /**
     * Set trigger
     *
     * @param string $trigger
     * @return Filter
     */
    public function setTrigger($trigger)
    {
        $this->trigger = $trigger;

        return $this;
    }

    /**
     * Get trigger
     *
     * @return string 
     */
    public function getTrigger()
    {
        return $this->trigger;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Filter
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
     * Set filter_type
     *
     * @param integer $filterType
     * @return Filter
     */
    public function setFilterType($filterType)
    {
        $this->filter_type = $filterType;

        return $this;
    }

    /**
     * Get filter_type
     *
     * @return integer 
     */
    public function getFilterType()
    {
        return $this->filter_type;
    }

    /**
     * Set owner
     *
     * @param \Arii\UserBundle\Entity\User $owner
     * @return Filter
     */
    public function setOwner(\Arii\UserBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Arii\UserBundle\Entity\User 
     */
    public function getOwner()
    {
        return $this->owner;
    }
}
