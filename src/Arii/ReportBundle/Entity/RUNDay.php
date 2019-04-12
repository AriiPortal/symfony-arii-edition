<?php

namespace Arii\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Autosys
 *
 * @ORM\Table(name="REPORT_RUN_DAY")
 * @ORM\Entity(repositoryClass="Arii\ReportBundle\Entity\RUNDayRepository")
 */
class RUNDay
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
     * @ORM\Column(name="app", type="string", length=64, nullable=true)
     */
    private $app;
    
    /**
     * @var string
     *
     * @ORM\Column(name="env", type="string", length=12, nullable=true)
     */
    private $env;

    /**
     * @var integer
     *
     * @ORM\Column(name="jobClass", type="string", length=24, nullable=true)
     */
    private $jobClass;
    
    /**
     * @var datetime
     *
     * @ORM\Column(name="run_date", type="datetime", length=8, nullable=true)
     */
    private $date;    
        
    /**
     * @var string
     *
     * @ORM\Column(name="spooler_name", type="string", length=32, nullable=true)
     */
    private $spooler_name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="executions", type="integer", nullable=true)
     */
    private $executions=0;

    /**
     * @var integer
     *
     * @ORM\Column(name="warnings", type="integer", nullable=true)
     */
    private $warnings=0;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="alarms", type="integer", nullable=true)
     */
    private $alarms=0;

    /**
     * @var integer
     *
     * @ORM\Column(name="acks", type="integer", nullable=true)
     */
    private $acks=0;

    /**
     * @var integer
     *
     * @ORM\Column(name="issues", type="integer", nullable=true)
     */
    private $issues=0;
    
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
     * Set app
     *
     * @param string $app
     * @return RUNDay
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
     * @return RUNDay
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
     * @return RUNDay
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
     * @return RUNDay
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
     * Set executions
     *
     * @param string $executions
     * @return RUNDay
     */
    public function setExecutions($executions)
    {
        $this->executions = $executions;

        return $this;
    }

    /**
     * Get executions
     *
     * @return string 
     */
    public function getExecutions()
    {
        return $this->executions;
    }

    /**
     * Set warnings
     *
     * @param integer $warnings
     * @return RUNDay
     */
    public function setWarnings($warnings)
    {
        $this->warnings = $warnings;

        return $this;
    }

    /**
     * Get warnings
     *
     * @return integer 
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * Set alarms
     *
     * @param integer $alarms
     * @return RUNDay
     */
    public function setAlarms($alarms)
    {
        $this->alarms = $alarms;

        return $this;
    }

    /**
     * Get alarms
     *
     * @return integer 
     */
    public function getAlarms()
    {
        return $this->alarms;
    }

    /**
     * Set acks
     *
     * @param integer $acks
     * @return RUNDay
     */
    public function setAcks($acks)
    {
        $this->acks = $acks;

        return $this;
    }

    /**
     * Get acks
     *
     * @return integer 
     */
    public function getAcks()
    {
        return $this->acks;
    }

    /**
     * Set issues
     *
     * @param integer $issues
     * @return RUNDay
     */
    public function setIssues($issues)
    {
        $this->acks = $issues;

        return $this;
    }

    /**
     * Get issues
     *
     * @return integer 
     */
    public function getIssues()
    {
        return $this->issues;
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
    public function getjobClass()
    {
        return $this->jobClass;
    }

}
