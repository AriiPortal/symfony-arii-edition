<?php

namespace Arii\JIDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SchedulerOrderHistory
 *
 * @ORM\Table(name="SCHEDULER_ORDER_HISTORY", indexes={@ORM\Index(name="SCHEDULER_O_HISTORY_SPOOLER_ID", columns={"SPOOLER_ID"}), @ORM\Index(name="SCHEDULER_O_HISTORY_JOB_CHAIN", columns={"JOB_CHAIN", "ORDER_ID"}), @ORM\Index(name="SCHEDULER_O_HISTORY_START_TIME", columns={"START_TIME"})})
 * @ORM\Entity(readOnly=true,repositoryClass="Arii\JIDBundle\Entity\SchedulerOrderHistoryRepository")
 */
class SchedulerOrderHistory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="HISTORY_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $history;

    /**
     * @var string
     *
     * @ORM\Column(name="JOB_CHAIN", type="string", length=255, nullable=false)
     */
    private $jobChain;

    /**
     * @var string
     *
     * @ORM\Column(name="ORDER_ID", type="string", length=255, nullable=false)
     */
    private $orderId;

    /**
     * @var string
     *
     * @ORM\Column(name="SPOOLER_ID", type="string", length=100, nullable=true)
     */
    private $spoolerId;

    /**
     * @var string
     *
     * @ORM\Column(name="TITLE", type="string", length=200, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="STATE", type="string", length=100, nullable=true)
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="STATE_TEXT", type="string", length=100, nullable=true)
     */
    private $stateText;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="START_TIME", type="datetime", nullable=false)
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="END_TIME", type="datetime", nullable=true)
     */
    private $endTime;

    /**
     * @var string
     *
     * @ORM\Column(name="LOG", type="blob", nullable=true)
     */
    private $log;



    /**
     * Get history
     *
     * @return integer 
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * Set jobChain
     *
     * @param string $jobChain
     * @return SchedulerOrderHistory
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
     * @return SchedulerOrderHistory
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
     * Set spoolerId
     *
     * @param string $spoolerId
     * @return SchedulerOrderHistory
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
     * Set title
     *
     * @param string $title
     * @return SchedulerOrderHistory
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
     * Set state
     *
     * @param string $state
     * @return SchedulerOrderHistory
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
     * @return SchedulerOrderHistory
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
     * Set startTime
     *
     * @param \DateTime $startTime
     * @return SchedulerOrderHistory
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
     * @return SchedulerOrderHistory
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
     * @return SchedulerOrderHistory
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
