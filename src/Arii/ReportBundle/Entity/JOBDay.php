<?php

namespace Arii\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Autosys
 *
 * @ORM\Table(name="REPORT_JOB_DAY",indexes={@ORM\Index(name="search_idx", columns={"application", "env", "job_date"})})
 * @ORM\Entity(repositoryClass="Arii\ReportBundle\Entity\JOBDayRepository")
 */
class JOBDay
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
     * @ORM\Column(name="application", type="string", length=64, nullable=true)
     */
    private $application;
    
    /**
     * @var string
     *
     * @ORM\Column(name="env", type="string", length=12, nullable=true)
     */
    private $env;

    /**
     * @var datetime
     *
     * @ORM\Column(name="job_date", type="datetime", length=8, nullable=true)
     */
    private $date;    
        
    /**
     * @var string
     *
     * @ORM\Column(name="spooler_name", type="string", length=32, nullable=true)
     */
    private $spooler_name;
    
    // Instance ou Id du spooler
    /**
     * @var string
     *
     * @ORM\Column(name="spooler_type", type="string", length=12, nullable=true)
     */
    private $spooler_type;
    
    /**
     * @var string
     *
     * @ORM\Column(name="jobs", type="integer", nullable=true)
     */
    private $jobs=0;

    /**
     * @var integer
     *
     * @ORM\Column(name="created", type="integer", nullable=true)
     */
    private $created=0;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="deleted", type="integer", nullable=true)
     */
    private $deleted=0;
    
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
     * Set application
     *
     * @param string $application
     * @return JOBDay
     */
    public function setApplication($application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get application
     *
     * @return string 
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Set env
     *
     * @param string $env
     * @return JOBDay
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
     * Set date
     *
     * @param \DateTime $date
     * @return JOBDay
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set spooler_name
     *
     * @param string $spoolerName
     * @return JOBDay
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
     * Set spooler_type
     *
     * @param string $spoolerType
     * @return JOBDay
     */
    public function setSpoolerType($spoolerType)
    {
        $this->spooler_type = $spoolerType;

        return $this;
    }

    /**
     * Get spooler_type
     *
     * @return string 
     */
    public function getSpoolerType()
    {
        return $this->spooler_type;
    }

    /**
     * Set jobs
     *
     * @param string $jobs
     * @return JOBDay
     */
    public function setJobs($jobs)
    {
        $this->jobs = $jobs;

        return $this;
    }

    /**
     * Get jobs
     *
     * @return string 
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * Set created
     *
     * @param integer $created
     * @return JOBDay
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return integer 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set deleted
     *
     * @param integer $deleted
     * @return JOBDay
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return integer 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }
}
