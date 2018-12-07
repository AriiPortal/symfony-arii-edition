<?php

namespace Arii\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActivityRuns
 *
 * @ORM\Table(name="REPORT_ACTIVITY_RUNS")
 * @ORM\Entity()
 */
class ActivityRuns
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
     * @ORM\ManyToOne(targetEntity="Arii\ReportBundle\Entity\ActivityStatus")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     * 
     */
    private $activity;

    /**
     * @var integer
     *
     * @ORM\Column(name="step", type="integer", nullable=false)
     */
    private $step;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_time", type="datetime", nullable=false)
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_time", type="datetime", nullable=true)
     */
    private $endTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="error", type="integer", nullable=true)
     */
    private $error;

    /**
     * @var string
     *
     * @ORM\Column(name="error_code", type="string", length=50, nullable=true)
     */
    private $errorCode;

    /**
     * @var string
     *
     * @ORM\Column(name="error_text", type="string", length=250, nullable=true)
     */
    private $errorText;

    /**
     * @var string
     *
     * @ORM\Column(name="cluster_member_id", type="string", length=100, nullable=true)
     */
    private $clusterMemberId;

    /**
     * @var string
     *
     * @ORM\Column(name="job_name", type="string", length=255, nullable=true)
     */
    private $jobName;

    /**
     * @var integer
     *
     * @ORM\Column(name="steps", type="integer", nullable=true)
     */
    private $steps;

    /**
     * @var integer
     *
     * @ORM\Column(name="exit_code", type="integer", nullable=true)
     */
    private $exitCode;

    /**
     * @var string
     *
     * @ORM\Column(name="parameters", type="text", nullable=true)
     */
    private $parameters;

    /**
     * @var string
     *
     * @ORM\Column(name="log", type="blob", nullable=true)
     */
    private $log;

    /**
     * @var integer
     *
     * @ORM\Column(name="pid", type="integer", nullable=true)
     */
    private $pid;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id_order", type="integer")
     */
    private $idOrder;
    /**
     * @var integer
     *
     * @ORM\Column(name="id_history", type="integer")
     */
    private $idHistory;

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
     * Set step
     *
     * @param integer $step
     * @return ActivityRuns
     */
    public function setStep($step)
    {
        $this->step = $step;

        return $this;
    }

    /**
     * Get step
     *
     * @return integer 
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     * @return ActivityRuns
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime 
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     * @return ActivityRuns
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime 
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set error
     *
     * @param integer $error
     * @return ActivityRuns
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Get error
     *
     * @return integer 
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set errorCode
     *
     * @param string $errorCode
     * @return ActivityRuns
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    /**
     * Get errorCode
     *
     * @return string 
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * Set errorText
     *
     * @param string $errorText
     * @return ActivityRuns
     */
    public function setErrorText($errorText)
    {
        $this->errorText = $errorText;

        return $this;
    }

    /**
     * Get errorText
     *
     * @return string 
     */
    public function getErrorText()
    {
        return $this->errorText;
    }

    /**
     * Set clusterMemberId
     *
     * @param string $clusterMemberId
     * @return ActivityRuns
     */
    public function setClusterMemberId($clusterMemberId)
    {
        $this->clusterMemberId = $clusterMemberId;

        return $this;
    }

    /**
     * Get clusterMemberId
     *
     * @return string 
     */
    public function getClusterMemberId()
    {
        return $this->clusterMemberId;
    }

    /**
     * Set jobName
     *
     * @param string $jobName
     * @return ActivityRuns
     */
    public function setJobName($jobName)
    {
        $this->jobName = $jobName;

        return $this;
    }

    /**
     * Get jobName
     *
     * @return string 
     */
    public function getJobName()
    {
        return $this->jobName;
    }

    /**
     * Set steps
     *
     * @param integer $steps
     * @return ActivityRuns
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
     * Set exitCode
     *
     * @param integer $exitCode
     * @return ActivityRuns
     */
    public function setExitCode($exitCode)
    {
        $this->exitCode = $exitCode;

        return $this;
    }

    /**
     * Get exitCode
     *
     * @return integer 
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }

    /**
     * Set parameters
     *
     * @param string $parameters
     * @return ActivityRuns
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Get parameters
     *
     * @return string 
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Set log
     *
     * @param string $log
     * @return ActivityRuns
     */
    public function setLog($log)
    {
        $this->log = $log;

        return $this;
    }

    /**
     * Get log
     *
     * @return string 
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Set pid
     *
     * @param integer $pid
     * @return ActivityRuns
     */
    public function setPid($pid)
    {
        $this->pid = $pid;

        return $this;
    }

    /**
     * Get pid
     *
     * @return integer 
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Set idOrder
     *
     * @param integer $idOrder
     * @return ActivityRuns
     */
    public function setIdOrder($idOrder)
    {
        $this->idOrder = $idOrder;

        return $this;
    }

    /**
     * Get idOrder
     *
     * @return integer 
     */
    public function getIdOrder()
    {
        return $this->idOrder;
    }

    /**
     * Set idHistory
     *
     * @param integer $idHistory
     * @return ActivityRuns
     */
    public function setIdHistory($idHistory)
    {
        $this->idHistory = $idHistory;

        return $this;
    }

    /**
     * Get idHistory
     *
     * @return integer 
     */
    public function getIdHistory()
    {
        return $this->idHistory;
    }

    /**
     * Set activity
     *
     * @param \Arii\ReportBundle\Entity\ActivityStatus $activity
     * @return ActivityRuns
     */
    public function setActivity(\Arii\ReportBundle\Entity\ActivityStatus $activity = null)
    {
        $this->activity = $activity;

        return $this;
    }

    /**
     * Get activity
     *
     * @return \Arii\ReportBundle\Entity\ActivityStatus 
     */
    public function getActivity()
    {
        return $this->activity;
    }
}
