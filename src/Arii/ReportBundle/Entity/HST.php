<?php

namespace Arii\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Autosys
 *
 * @ORM\Table(name="REPORT_HST",indexes={@ORM\Index(name="hst_idx", columns={"spooler_name", "job_name", "start_time"})})
 * @ORM\Entity(repositoryClass="Arii\ReportBundle\Entity\RUNRepository")
 */
class HST
{
    /**
     * @var bigint
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="spooler_name", type="string", length=32)
     */
    private $spooler_name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="job_name", type="string", length=255)
     */
    private $job_name;

    /**
     * @var string
     *
     * @ORM\Column(name="job_trigger", type="string", length=255, nullable=true)
     */
    private $job_trigger;
    
    /**
     * @ORM\ManyToOne(targetEntity="Arii\ReportBundle\Entity\JOB")
     * @ORM\JoinColumn(nullable=true)
     **/
    private $job;
    
    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=64, nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="machine", type="string", length=32, nullable=true)
     */
    private $machine;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_time", type="datetime", nullable=true)
     */
    private $start_time;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_time", type="datetime", nullable=true)
     */
    private $end_time;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="run", type="integer", nullable=true)
     */
    private $run;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="try", type="integer", nullable=true)
     */
    private $try;

    /**
     * @var integer
     *
     * @ORM\Column(name="exit_code", type="integer", nullable=true)
     */
    private $exit_code;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="alarm_time", type="datetime", nullable=true)
     */
    private $alarm_time;
    
    /**
     * @var string
     *
     * @ORM\Column(name="alarm", type="string", length=24, nullable=true)
     */
    private $alarm;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ack_time", type="datetime", nullable=true)
     */
    private $ack_time;

    /**
     * @var string
     *
     * @ORM\Column(name="ack", type="string", length=24, nullable=true)
     */
    private $ack;
        
    /**
     * @var string
     *
     * @ORM\Column(name="message", type="string", length=255, nullable=true)
     */
    private $message;
    
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
     * @return RUN
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
     * Set job_trigger
     *
     * @param string $jobName
     * @return RUN
     */
    public function setJobTrigger($jobName)
    {
        $this->job_trigger = $jobName;

        return $this;
    }

    /**
     * Get job_trigger
     *
     * @return string 
     */
    public function getJobTrigger()
    {
        return $this->job_trigger;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return RUN
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
     * Set machine
     *
     * @param string $machine
     * @return RUN
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
     * Set start_time
     *
     * @param \DateTime $startTime
     * @return RUN
     */
    public function setStartTime($startTime)
    {
        $this->start_time = $startTime;

        return $this;
    }

    /**
     * Get start_time
     *
     * @return \DateTime 
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * Set end_time
     *
     * @param \DateTime $endTime
     * @return RUN
     */
    public function setEndTime($endTime)
    {
        $this->end_time = $endTime;

        return $this;
    }

    /**
     * Get end_time
     *
     * @return \DateTime 
     */
    public function getEndTime()
    {
        return $this->end_time;
    }

    /**
     * Set run
     *
     * @param integer $run
     * @return RUN
     */
    public function setRun($run)
    {
        $this->run = $run;

        return $this;
    }

    /**
     * Get run
     *
     * @return integer 
     */
    public function getRun()
    {
        return $this->run;
    }

    /**
     * Set try
     *
     * @param integer $try
     * @return RUN
     */
    public function setTry($try)
    {
        $this->try = $try;

        return $this;
    }

    /**
     * Get try
     *
     * @return integer 
     */
    public function getTry()
    {
        return $this->try;
    }

    /**
     * Set exit_code
     *
     * @param integer $exitCode
     * @return RUN
     */
    public function setExitCode($exitCode)
    {
        $this->exit_code = $exitCode;

        return $this;
    }

    /**
     * Get exit_code
     *
     * @return integer 
     */
    public function getExitCode()
    {
        return $this->exit_code;
    }

    /**
     * Set alarm_time
     *
     * @param \DateTime $alarmTime
     * @return RUN
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
     * Set alarm
     *
     * @param string $alarm
     * @return RUN
     */
    public function setAlarm($alarm)
    {
        $this->alarm = $alarm;

        return $this;
    }

    /**
     * Get alarm
     *
     * @return string 
     */
    public function getAlarm()
    {
        return $this->alarm;
    }

    /**
     * Set ack_time
     *
     * @param \DateTime $ackTime
     * @return RUN
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
     * Set ack
     *
     * @param string $ack
     * @return RUN
     */
    public function setAck($ack)
    {
        $this->ack = $ack;

        return $this;
    }

    /**
     * Get ack
     *
     * @return string 
     */
    public function getAck()
    {
        return $this->ack;
    }

    /**
     * Set job
     *
     * @param \Arii\ReportBundle\Entity\JOB $job
     * @return RUN
     */
    public function setJob(\Arii\ReportBundle\Entity\JOB $job = null)
    {
        $this->job = $job;

        return $this;
    }

    /**
     * Get job
     *
     * @return \Arii\ReportBundle\Entity\JOB 
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * Set spooler_name
     *
     * @param string $spoolerName
     * @return RUN
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
     * Set updated
     *
     * @param \DateTime $updated
     * @return RUN
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
     * Set message
     *
     * @param string $message
     * @return RUN
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }
    
}
