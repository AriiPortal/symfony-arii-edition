<?php

namespace Arii\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * JOB
 *
 * @ORM\Table(name="REPORT_JOB",indexes={@ORM\Index(name="job_idx", columns={"spooler_name", "job_name"})})
 * @ORM\Entity(repositoryClass="Arii\ReportBundle\Entity\JOBRepository")
 */
class JOB
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
     * @ORM\Column(name="job_name", type="string", length=128)
     */
    private $job_name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="job_type", type="string", length=24)
     */
    private $job_type;

    /**
     * @var integer
     *
     * @ORM\Column(name="jobClass", type="string", length=24, nullable=true)
     */
    private $jobClass;
    
    /**
     * @var string
     *
     * @ORM\Column(name="command", type="string", length=1024, nullable=true )
     */
    private $command;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=1024, nullable=true )
     */
    private $description;
        
    /**
     * @var string
     *
     * @ORM\Column(name="watch_file", type="string", length=255, nullable=true )
     */
    private $watch_file;
    
    /**
     * @var string
     *
     * @ORM\Column(name="run_as", type="string", length=255, nullable=true)
     */
    private $user;
    
    /**
     * @var string
     *
     * @ORM\Column(name="machine", type="string", length=64, nullable=true)
     */
    private $machine;
        
// Origine    
    /**
     * @var string
     *
     * @ORM\Column(name="spooler_name", type="string", length=32)
     */
    private $spooler_name;

// Organisation
    
    /**
     * @var string
     *
     * @ORM\Column(name="box_name", type="string", length=128, nullable=true)
     */
    private $box_name;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Arii\ReportBundle\Entity\BOX")
     * @ORM\JoinColumn(nullable=true)
     * @ORM\JoinTable(name="REPORT_JOB__BOX" )
     */
    protected $box;

    /**
     * @var string
     *
     * @ORM\Column(name="app", type="string", length=12, nullable=true)
     */
    private $app;
    
    /**
     * @var string
     *
     * @ORM\Column(name="env", type="string", length=12, nullable=true)
     */
    private $env;

// Liens
    
    /**
     * @var string
     *
     * @ORM\Column(name="lnk_formula", type="string", length=1024, nullable=true)
     */
    private $lnk_formula;

// Suivi
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="first_execution", type="datetime", nullable=true)
     */
    private $first_execution;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_execution", type="datetime", nullable=true)
     */
    private $last_execution;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_change", type="datetime", nullable=true)
     */
    private $last_change;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted", type="datetime", nullable=true)
     */
    private $deleted = null;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    private $updated;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->box = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set job_name
     *
     * @param string $jobName
     * @return JOB
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
     * Set job_type
     *
     * @param string $jobType
     * @return JOB
     */
    public function setJobType($jobType)
    {
        $this->job_type = $jobType;

        return $this;
    }

    /**
     * Get job_type
     *
     * @return string 
     */
    public function getJobType()
    {
        return $this->job_type;
    }

    /**
     * Set jobClass
     *
     * @param string $jobClass
     * @return JOB
     */
    public function setJobClass($jobClass)
    {
        $this->jobClass = $jobClass;

        return $this;
    }

    /**
     * Get jobClass
     *
     * @return string 
     */
    public function getJobClass()
    {
        return $this->jobClass;
    }

    /**
     * Set command
     *
     * @param string $command
     * @return JOB
     */
    public function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Get command
     *
     * @return string 
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return JOB
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
     * Set watch_file
     *
     * @param string $watchFile
     * @return JOB
     */
    public function setWatchFile($watchFile)
    {
        $this->watch_file = $watchFile;

        return $this;
    }

    /**
     * Get watch_file
     *
     * @return string 
     */
    public function getWatchFile()
    {
        return $this->watch_file;
    }

    /**
     * Set user
     *
     * @param string $user
     * @return JOB
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
     * Set machine
     *
     * @param string $machine
     * @return JOB
     */
    public function setMachine($machine)
    {
        $this->machine = $machine;

        return $this;
    }

    /**
     * Get machine
     *
     * @return string 
     */
    public function getMachine()
    {
        return $this->machine;
    }

    /**
     * Set spooler_name
     *
     * @param string $spoolerName
     * @return JOB
     */
    public function setSpoolerName($spoolerName)
    {
        $this->spooler_name = $spoolerName;

        return $this;
    }

    /**
     * Get spooler_name
     *
     * @return string 
     */
    public function getSpoolerName()
    {
        return $this->spooler_name;
    }

    /**
     * Set box_name
     *
     * @param string $boxName
     * @return JOB
     */
    public function setBoxName($boxName)
    {
        $this->box_name = $boxName;

        return $this;
    }

    /**
     * Get box_name
     *
     * @return string 
     */
    public function getBoxName()
    {
        return $this->box_name;
    }

    /**
     * Set app
     *
     * @param string $app
     * @return JOB
     */
    public function setApp($app)
    {
        $this->app = $app;

        return $this;
    }

    /**
     * Get app
     *
     * @return string 
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * Set env
     *
     * @param string $env
     * @return JOB
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
     * Set lnk_formula
     *
     * @param string $lnkFormula
     * @return JOB
     */
    public function setLnkFormula($lnkFormula)
    {
        $this->lnk_formula = $lnkFormula;

        return $this;
    }

    /**
     * Get lnk_formula
     *
     * @return string 
     */
    public function getLnkFormula()
    {
        return $this->lnk_formula;
    }

    /**
     * Set first_execution
     *
     * @param \DateTime $firstExecution
     * @return JOB
     */
    public function setFirstExecution($firstExecution)
    {
        $this->first_execution = $firstExecution;

        return $this;
    }

    /**
     * Get first_execution
     *
     * @return \DateTime 
     */
    public function getFirstExecution()
    {
        return $this->first_execution;
    }

    /**
     * Set last_execution
     *
     * @param \DateTime $lastExecution
     * @return JOB
     */
    public function setLastExecution($lastExecution)
    {
        $this->last_execution = $lastExecution;

        return $this;
    }

    /**
     * Get last_execution
     *
     * @return \DateTime 
     */
    public function getLastExecution()
    {
        return $this->last_execution;
    }

    /**
     * Set last_change
     *
     * @param \DateTime $lastChange
     * @return JOB
     */
    public function setLastChange($lastChange)
    {
        $this->last_change = $lastChange;

        return $this;
    }

    /**
     * Get last_change
     *
     * @return \DateTime 
     */
    public function getLastChange()
    {
        return $this->last_change;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return JOB
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set deleted
     *
     * @param \DateTime $deleted
     * @return JOB
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return \DateTime 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return JOB
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
     * Add box
     *
     * @param \Arii\ReportBundle\Entity\BOX $box
     * @return JOB
     */
    public function addBox(\Arii\ReportBundle\Entity\BOX $box)
    {
        $this->box[] = $box;

        return $this;
    }

    /**
     * Remove box
     *
     * @param \Arii\ReportBundle\Entity\BOX $box
     */
    public function removeBox(\Arii\ReportBundle\Entity\BOX $box)
    {
        $this->box->removeElement($box);
    }

    /**
     * Get box
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBox()
    {
        return $this->box;
    }
}
