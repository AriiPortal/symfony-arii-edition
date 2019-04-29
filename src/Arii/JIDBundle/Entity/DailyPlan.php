<?php

namespace Arii\JIDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DailyPlan
 *
 * @ORM\Table(name="daily_plan", uniqueConstraints={@ORM\UniqueConstraint(name="DAILY_PLAN_UNIQUE", columns={"SCHEDULER_ID", "JOB", "JOB_CHAIN", "ORDER_ID", "PLANNED_START"})})
 * @ORM\Entity(readOnly=true,repositoryClass="Arii\JIDBundle\Entity\DailyPlanRepository")
 */
class DailyPlan
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
     * @var string
     *
     * @ORM\Column(name="SCHEDULER_ID", type="string", length=100, nullable=false)
     */
    private $schedulerId;

    /**
     * @var string
     *
     * @ORM\Column(name="JOB", type="string", length=255, nullable=false)
     */
    private $job;

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
     * @var \DateTime
     *
     * @ORM\Column(name="PLANNED_START", type="datetime", nullable=false)
     */
    private $plannedStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="EXPECTED_END", type="datetime", nullable=true)
     */
    private $expectedEnd;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="PERIOD_BEGIN", type="datetime", nullable=true)
     */
    private $periodBegin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="PERIOD_END", type="datetime", nullable=true)
     */
    private $periodEnd;

    /**
     * @var integer
     *
     * @ORM\Column(name="REPEAT_INTERVAL", type="integer", nullable=true)
     */
    private $repeatInterval;

    /**
     * @var integer
     *
     * @ORM\Column(name="START_START", type="integer", nullable=false)
     */
    private $startStart;

    /**
     * @var integer
     *
     * @ORM\Column(name="IS_ASSIGNED", type="integer", nullable=false)
     */
    private $isAssigned;

    /**
     * @var integer
     *
     * @ORM\Column(name="IS_LATE", type="integer", nullable=false)
     */
    private $isLate;

    /**
     * @var string
     *
     * @ORM\Column(name="STATE", type="string", length=30, nullable=true)
     */
    private $state;

    /**
     * @var integer
     *
     * @ORM\Column(name="REPORT_TRIGGER_ID", type="integer", nullable=true)
     */
    private $reportTriggerId;

    /**
     * @var integer
     *
     * @ORM\Column(name="REPORT_EXECUTIONS_ID", type="integer", nullable=true)
     */
    private $reportExecutionsId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="CREATED", type="datetime", nullable=false)
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="MODIFIED", type="datetime", nullable=false)
     */
    private $modified;



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
     * Set schedulerId
     *
     * @param string $schedulerId
     * @return DailyPlan
     */
    public function setSchedulerId($schedulerId)
    {
        $this->schedulerId = $schedulerId;

        return $this;
    }

    /**
     * Get schedulerId
     *
     * @return string 
     */
    public function getSchedulerId()
    {
        return $this->schedulerId;
    }

    /**
     * Set job
     *
     * @param string $job
     * @return DailyPlan
     */
    public function setJob($job)
    {
        $this->job = $job;

        return $this;
    }

    /**
     * Get job
     *
     * @return string 
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * Set jobChain
     *
     * @param string $jobChain
     * @return DailyPlan
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
     * @return DailyPlan
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
     * Set plannedStart
     *
     * @param \DateTime $plannedStart
     * @return DailyPlan
     */
    public function setPlannedStart($plannedStart)
    {
        $this->plannedStart = $plannedStart;

        return $this;
    }

    /**
     * Get plannedStart
     *
     * @return \DateTime 
     */
    public function getPlannedStart()
    {
        return $this->plannedStart;
    }

    /**
     * Set expectedEnd
     *
     * @param \DateTime $expectedEnd
     * @return DailyPlan
     */
    public function setExpectedEnd($expectedEnd)
    {
        $this->expectedEnd = $expectedEnd;

        return $this;
    }

    /**
     * Get expectedEnd
     *
     * @return \DateTime 
     */
    public function getExpectedEnd()
    {
        return $this->expectedEnd;
    }

    /**
     * Set periodBegin
     *
     * @param \DateTime $periodBegin
     * @return DailyPlan
     */
    public function setPeriodBegin($periodBegin)
    {
        $this->periodBegin = $periodBegin;

        return $this;
    }

    /**
     * Get periodBegin
     *
     * @return \DateTime 
     */
    public function getPeriodBegin()
    {
        return $this->periodBegin;
    }

    /**
     * Set periodEnd
     *
     * @param \DateTime $periodEnd
     * @return DailyPlan
     */
    public function setPeriodEnd($periodEnd)
    {
        $this->periodEnd = $periodEnd;

        return $this;
    }

    /**
     * Get periodEnd
     *
     * @return \DateTime 
     */
    public function getPeriodEnd()
    {
        return $this->periodEnd;
    }

    /**
     * Set repeatInterval
     *
     * @param integer $repeatInterval
     * @return DailyPlan
     */
    public function setRepeatInterval($repeatInterval)
    {
        $this->repeatInterval = $repeatInterval;

        return $this;
    }

    /**
     * Get repeatInterval
     *
     * @return integer 
     */
    public function getRepeatInterval()
    {
        return $this->repeatInterval;
    }

    /**
     * Set startStart
     *
     * @param integer $startStart
     * @return DailyPlan
     */
    public function setStartStart($startStart)
    {
        $this->startStart = $startStart;

        return $this;
    }

    /**
     * Get startStart
     *
     * @return integer 
     */
    public function getStartStart()
    {
        return $this->startStart;
    }

    /**
     * Set isAssigned
     *
     * @param integer $isAssigned
     * @return DailyPlan
     */
    public function setIsAssigned($isAssigned)
    {
        $this->isAssigned = $isAssigned;

        return $this;
    }

    /**
     * Get isAssigned
     *
     * @return integer 
     */
    public function getIsAssigned()
    {
        return $this->isAssigned;
    }

    /**
     * Set isLate
     *
     * @param integer $isLate
     * @return DailyPlan
     */
    public function setIsLate($isLate)
    {
        $this->isLate = $isLate;

        return $this;
    }

    /**
     * Get isLate
     *
     * @return integer 
     */
    public function getIsLate()
    {
        return $this->isLate;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return DailyPlan
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
     * Set reportTriggerId
     *
     * @param integer $reportTriggerId
     * @return DailyPlan
     */
    public function setReportTriggerId($reportTriggerId)
    {
        $this->reportTriggerId = $reportTriggerId;

        return $this;
    }

    /**
     * Get reportTriggerId
     *
     * @return integer 
     */
    public function getReportTriggerId()
    {
        return $this->reportTriggerId;
    }

    /**
     * Set reportExecutionsId
     *
     * @param integer $reportExecutionsId
     * @return DailyPlan
     */
    public function setReportExecutionsId($reportExecutionsId)
    {
        $this->reportExecutionsId = $reportExecutionsId;

        return $this;
    }

    /**
     * Get reportExecutionsId
     *
     * @return integer 
     */
    public function getReportExecutionsId()
    {
        return $this->reportExecutionsId;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return DailyPlan
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
     * Set modified
     *
     * @param \DateTime $modified
     * @return DailyPlan
     */
    public function setModified($modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * Get modified
     *
     * @return \DateTime 
     */
    public function getModified()
    {
        return $this->modified;
    }
}
