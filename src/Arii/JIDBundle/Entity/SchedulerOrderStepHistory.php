<?php

namespace Arii\JIDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SchedulerOrderStepHistory
 *
 * @ORM\Table(name="scheduler_order_step_history", indexes={@ORM\Index(name="SCHEDULER_OSH_TASK_ID", columns={"TASK_ID"})})
 * @ORM\Entity(readOnly=true,repositoryClass="Arii\JIDBundle\Entity\SchedulerOrderStepHistoryRepository")
 */
class SchedulerOrderStepHistory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Arii\JIDBundle\Entity\SchedulerOrderHistory")
     * @ORM\JoinColumn(name="HISTORY_ID", referencedColumnName="HISTORY_ID", nullable=true)
     * 
     */
    private $history;

    /**
     * @ORM\ManyToOne(targetEntity="Arii\JIDBundle\Entity\SchedulerHistory")
     * @ORM\JoinColumn(name="TASK_ID", referencedColumnName="ID", nullable=true)
     *      
     */
    private $task;

    /**
     * @var integer
     *
     * @ORM\Column(name="STEP", type="integer", nullable=false)
     */
    private $step;

    /**
     * @var string
     *
     * @ORM\Column(name="STATE", type="string", length=100, nullable=true)
     */
    private $state;

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
     * @var integer
     *
     * @ORM\Column(name="ERROR", type="integer", nullable=true)
     */
    private $error;

    /**
     * @var string
     *
     * @ORM\Column(name="ERROR_CODE", type="string", length=50, nullable=true)
     */
    private $errorCode;

    /**
     * @var string
     *
     * @ORM\Column(name="ERROR_TEXT", type="string", length=250, nullable=true)
     */
    private $errorText;

    /**
     * Set history
     *
     * @param \Arii\JIDBundle\Entity\SchedulerOrderHistory $history
     * @return History
     */
    public function setHistory(\Arii\JIDBundle\Entity\SchedulerOrderHistory $history = null)
    {
        $this->history = $history;

        return $this;
    }

    /**
     * Get history
     *
     * @return \Arii\JIDBundle\Entity\SchedulerOrderHistory 
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * Set task
     *
     * @param \Arii\JIDBundle\Entity\SchedulerHistory $task
     * @return Task
     */
    public function setTask(\Arii\JIDBundle\Entity\SchedulerHistory $task = null)
    {
        $this->task = $task;

        return $this;
    }

    /**
     * Get task
     *
     * @return \Arii\JIDBundle\Entity\SchedulerHistory 
     */
    public function getTask()
    {
        return $this->task;
    }
    
    /**
     * Set step
     *
     * @param integer $step
     * @return SchedulerOrderStepHistory
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
     * Set state
     *
     * @param string $state
     * @return SchedulerOrderStepHistory
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
     * Set startTime
     *
     * @param \DateTime $startTime
     * @return SchedulerOrderStepHistory
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
     * @return SchedulerOrderStepHistory
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
     * @return SchedulerOrderStepHistory
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
     * @return SchedulerOrderStepHistory
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
     * @return SchedulerOrderStepHistory
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
