<?php

namespace Arii\MFTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Connections
 *
 * @ORM\Table(name="MFT_DELIVERIES")
 * @ORM\Entity(readOnly=true,repositoryClass="Arii\MFTBundle\Entity\DeliveriesRepository")
 */
class Deliveries
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
     * @ORM\ManyToOne(targetEntity="Arii\MFTBundle\Entity\Operations")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     **/
    private $operation;    

     /**
     * @ORM\ManyToOne(targetEntity="Arii\MFTBundle\Entity\History")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     **/
    private $history;    

    /**
     * @var integer
     *
     * @ORM\Column(name="run", type="integer", nullable=true )
     */        
    private $run; 
    
    /**
     * @var integer
     *
     * @ORM\Column(name="try", type="integer", nullable=true )
     */        
    private $try; 
    
    /**
     * @var datetime
     *
     * @ORM\Column(name="start_time", type="datetime", length=16 )
     */        
    private $start_time;

    /**
     * @var datetime
     *
     * @ORM\Column(name="end_time", type="datetime" )
     */        
    private $end_time;

    /**
     * @var integer
     *
     * @ORM\Column(name="duration", type="integer" )
     */        
    private $duration;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=16)
     */        
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="status_text", type="string", length=24)
     */        
    private $status_text;

    /**
     * @var integer
     *
     * @ORM\Column(name="files_count", type="integer")
     */
    private $files_count;

    /**
     * @var integer
     *
     * @ORM\Column(name="files_size", type="integer")
     */
    private $files_size;

    /**
     * @var integer
     *
     * @ORM\Column(name="success", type="integer")
     */
    private $success;

    /**
     * @var integer
     *
     * @ORM\Column(name="skipped", type="integer")
     */
    private $skipped;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="failed", type="integer")
     */
    private $failed;
    
    /**
     * @var string
     *
     * @ORM\Column(name="last_error", type="string", length=1024, nullable=true)
     */
    private $last_error;
    
    /**
     * @var blob
     *
     * @ORM\Column(name="log_output", type="text", nullable=true )
     */
    private $log_output;

    /**
     * @var string
     *
     * @ORM\Column(name="log_name", type="string", length=1024, nullable=true )
     */
    private $log_name;

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
     * Set run
     *
     * @param integer $run
     * @return Deliveries
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
     * @return Deliveries
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
     * Set start_time
     *
     * @param \DateTime $startTime
     * @return Deliveries
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
     * @return Deliveries
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
     * Set duration
     *
     * @param integer $duration
     * @return Deliveries
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer 
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Deliveries
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
     * Set status_text
     *
     * @param string $statusText
     * @return Deliveries
     */
    public function setStatusText($statusText)
    {
        $this->status_text = $statusText;

        return $this;
    }

    /**
     * Get status_text
     *
     * @return string 
     */
    public function getStatusText()
    {
        return $this->status_text;
    }

    /**
     * Set files_count
     *
     * @param integer $files_count
     * @return Deliveries
     */
    public function setFilesCount($files_count)
    {
        $this->files_count = $files_count;

        return $this;
    }

    /**
     * Get files_count
     *
     * @return integer 
     */
    public function getFilesCount()
    {
        return $this->files_count;
    }

    /**
     * Set files_size
     *
     * @param integer $files_size
     * @return Deliveries
     */
    public function setFilesSize($files_size)
    {
        $this->files_size = $files_size;

        return $this;
    }

    /**
     * Get files_size
     *
     * @return integer 
     */
    public function getFilesSize()
    {
        return $this->files_size;
    }

    /**
     * Set success
     *
     * @param integer $success
     * @return Deliveries
     */
    public function setSuccess($success)
    {
        $this->success = $success;

        return $this;
    }

    /**
     * Get success
     *
     * @return integer 
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * Set skipped
     *
     * @param integer $skipped
     * @return Deliveries
     */
    public function setSkipped($skipped)
    {
        $this->skipped = $skipped;

        return $this;
    }

    /**
     * Get skipped
     *
     * @return integer 
     */
    public function getSkipped()
    {
        return $this->skipped;
    }

    /**
     * Set failed
     *
     * @param integer $failed
     * @return Deliveries
     */
    public function setFailed($failed)
    {
        $this->failed = $failed;

        return $this;
    }

    /**
     * Get failed
     *
     * @return integer 
     */
    public function getFailed()
    {
        return $this->failed;
    }

    /**
     * Set last_error
     *
     * @param string $lastError
     * @return Deliveries
     */
    public function setLastError($lastError)
    {
        $this->last_error = $lastError;

        return $this;
    }

    /**
     * Get last_error
     *
     * @return string 
     */
    public function getLastError()
    {
        return $this->last_error;
    }

    /**
     * Set log_output
     *
     * @param string $log
     * @return Deliveries
     */
    public function setLogOutput($log)
    {
        $this->log_output = $log;

        return $this;
    }

    /**
     * Get log_output
     *
     * @return string 
     */
    public function getLogOutput()
    {
        return $this->log_output;
    }

    /**
     * Set log_name
     *
     * @param string $logName
     * @return Deliveries
     */
    public function setLogName($logName)
    {
        $this->log_name = $logName;

        return $this;
    }

    /**
     * Get log_name
     *
     * @return string 
     */
    public function getLogName()
    {
        return $this->log_name;
    }

    /**
     * Set operation
     *
     * @param \Arii\MFTBundle\Entity\Operations $operation
     * @return Deliveries
     */
    public function setOperation(\Arii\MFTBundle\Entity\Operations $operation = null)
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * Get operation
     *
     * @return \Arii\MFTBundle\Entity\Operations 
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * Set history
     *
     * @param \Arii\MFTBundle\Entity\History $history
     * @return Deliveries
     */
    public function setHistory(\Arii\MFTBundle\Entity\History $history = null)
    {
        $this->history = $history;

        return $this;
    }

    /**
     * Get history
     *
     * @return \Arii\MFTBundle\Entity\History 
     */
    public function getHistory()
    {
        return $this->history;
    }
}
