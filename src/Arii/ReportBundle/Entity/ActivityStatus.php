<?php

namespace Arii\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActivityStatus
 *
 * @ORM\Table(name="REPORT_ACTIVITY_STATUS")
 * @ORM\Entity(repositoryClass="Arii\ReportBundle\Entity\ActivityStatusRepository")
 */
class ActivityStatus
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
     * @var integer
     *
     * @ORM\Column(name="id_db", type="integer")
     */
    private $idDb;

    /**
     * @var string
     *
     * @ORM\Column(name="spooler_id", type="string", length=100, nullable=false)
     */
    private $spoolerId;

    /**
     * @var string
     *
     * @ORM\Column(name="job_chain", type="string", length=255, nullable=false)
     */
    private $jobChain;
    
    /**
     * @var string
     *
     * @ORM\Column(name="order_id", type="string", length=100, nullable=false)
     */
    private $orderId;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=100, nullable=true)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="state_text", type="string", length=100, nullable=true)
     */
    private $stateText;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=200, nullable=true)
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_time", type="datetime", nullable=true)
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_time", type="datetime", nullable=true)
     */
    private $endTime;

    /**
     * @var string
     *
     * @ORM\Column(name="log", type="blob", nullable=true)
     */
    private $log;


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
     * Set idDb
     *
     * @param integer $idDb
     * @return ActivityStatus
     */
    public function setIdDb($idDb)
    {
        $this->idDb = $idDb;

        return $this;
    }

    /**
     * Get idDb
     *
     * @return integer 
     */
    public function getIdDb()
    {
        return $this->idDb;
    }

    /**
     * Set spoolerId
     *
     * @param string $spoolerId
     * @return ActivityStatus
     */
    public function setSpoolerId($spoolerId)
    {
        $this->spoolerId = $spoolerId;

        return $this;
    }

    /**
     * Get spoolerId
     *
     * @return string 
     */
    public function getSpoolerId()
    {
        return $this->spoolerId;
    }

    /**
     * Set jobChain
     *
     * @param string $jobChain
     * @return ActivityStatus
     */
    public function setJobChain($jobChain)
    {
        $this->jobChain = $jobChain;

        return $this;
    }

    /**
     * Get jobChain
     *
     * @return string 
     */
    public function getJobChain()
    {
        return $this->jobChain;
    }

    /**
     * Set orderId
     *
     * @param string $orderId
     * @return ActivityStatus
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return string 
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return ActivityStatus
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
     * Set stateText
     *
     * @param string $stateText
     * @return ActivityStatus
     */
    public function setStateText($stateText)
    {
        $this->stateText = $stateText;

        return $this;
    }

    /**
     * Get stateText
     *
     * @return string 
     */
    public function getStateText()
    {
        return $this->stateText;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return ActivityStatus
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
     * Set startTime
     *
     * @param \DateTime $startTime
     * @return ActivityStatus
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
     * @return ActivityStatus
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
     * Set log
     *
     * @param string $log
     * @return ActivityStatus
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
}
