<?php

namespace Arii\JIDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SchedulerOrders
 *
 * @ORM\Table(name="scheduler_orders")
 * @ORM\Entity(readOnly=true,repositoryClass="Arii\JIDBundle\Entity\SchedulerOrdersRepository")
 */
class SchedulerOrders
{
    /**
     * @var string
     *
     * @ORM\Column(name="SPOOLER_ID", type="string", length=100, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $spoolerId;

    /**
     * @var string
     *
     * @ORM\Column(name="JOB_CHAIN", type="string", length=255, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $jobChain;

    /**
     * @var string
     *
     * @ORM\Column(name="ID", type="string", length=255, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="PRIORITY", type="integer", nullable=false)
     */
    private $priority;

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
     * @var string
     *
     * @ORM\Column(name="TITLE", type="string", length=200, nullable=true)
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="CREATED_TIME", type="datetime", nullable=true)
     */
    private $createdTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="MOD_TIME", type="datetime", nullable=true)
     */
    private $modTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="ORDERING", type="integer", nullable=true)
     */
    private $ordering;

    /**
     * @var string
     *
     * @ORM\Column(name="PAYLOAD", type="text", nullable=true)
     */
    private $payload;

    /**
     * @var string
     *
     * @ORM\Column(name="RUN_TIME", type="text", nullable=true)
     */
    private $runTime;

    /**
     * @var string
     *
     * @ORM\Column(name="INITIAL_STATE", type="string", length=100, nullable=true)
     */
    private $initialState;

    /**
     * @var string
     *
     * @ORM\Column(name="ORDER_XML", type="text", nullable=true)
     */
    private $orderXml;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DISTRIBUTED_NEXT_TIME", type="datetime", nullable=true)
     */
    private $distributedNextTime;

    /**
     * @var string
     *
     * @ORM\Column(name="OCCUPYING_CLUSTER_MEMBER_ID", type="string", length=100, nullable=true)
     */
    private $occupyingClusterMemberId;



    /**
     * Set spoolerId
     *
     * @param string $spoolerId
     * @return SchedulerOrders
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
     * @return SchedulerOrders
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
     * Set id
     *
     * @param string $id
     * @return SchedulerOrders
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return string 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     * @return SchedulerOrders
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer 
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return SchedulerOrders
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
     * @return SchedulerOrders
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
     * @return SchedulerOrders
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
     * Set createdTime
     *
     * @param \DateTime $createdTime
     * @return SchedulerOrders
     */
    public function setCreatedTime($createdTime)
    {
        $this->createdTime = $createdTime;

        return $this;
    }

    /**
     * Get createdTime
     *
     * @return \DateTime 
     */
    public function getCreatedTime()
    {
        return $this->createdTime;
    }

    /**
     * Set modTime
     *
     * @param \DateTime $modTime
     * @return SchedulerOrders
     */
    public function setModTime($modTime)
    {
        $this->modTime = $modTime;

        return $this;
    }

    /**
     * Get modTime
     *
     * @return \DateTime 
     */
    public function getModTime()
    {
        return $this->modTime;
    }

    /**
     * Set ordering
     *
     * @param integer $ordering
     * @return SchedulerOrders
     */
    public function setOrdering($ordering)
    {
        $this->ordering = $ordering;

        return $this;
    }

    /**
     * Get ordering
     *
     * @return integer 
     */
    public function getOrdering()
    {
        return $this->ordering;
    }

    /**
     * Set payload
     *
     * @param string $payload
     * @return SchedulerOrders
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * Get payload
     *
     * @return string 
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Set runTime
     *
     * @param string $runTime
     * @return SchedulerOrders
     */
    public function setRunTime($runTime)
    {
        $this->runTime = $runTime;

        return $this;
    }

    /**
     * Get runTime
     *
     * @return string 
     */
    public function getRunTime()
    {
        return $this->runTime;
    }

    /**
     * Set initialState
     *
     * @param string $initialState
     * @return SchedulerOrders
     */
    public function setInitialState($initialState)
    {
        $this->initialState = $initialState;

        return $this;
    }

    /**
     * Get initialState
     *
     * @return string 
     */
    public function getInitialState()
    {
        return $this->initialState;
    }

    /**
     * Set orderXml
     *
     * @param string $orderXml
     * @return SchedulerOrders
     */
    public function setOrderXml($orderXml)
    {
        $this->orderXml = $orderXml;

        return $this;
    }

    /**
     * Get orderXml
     *
     * @return string 
     */
    public function getOrderXml()
    {
        return $this->orderXml;
    }

    /**
     * Set distributedNextTime
     *
     * @param \DateTime $distributedNextTime
     * @return SchedulerOrders
     */
    public function setDistributedNextTime($distributedNextTime)
    {
        $this->distributedNextTime = $distributedNextTime;

        return $this;
    }

    /**
     * Get distributedNextTime
     *
     * @return \DateTime 
     */
    public function getDistributedNextTime()
    {
        return $this->distributedNextTime;
    }

    /**
     * Set occupyingClusterMemberId
     *
     * @param string $occupyingClusterMemberId
     * @return SchedulerOrders
     */
    public function setOccupyingClusterMemberId($occupyingClusterMemberId)
    {
        $this->occupyingClusterMemberId = $occupyingClusterMemberId;

        return $this;
    }

    /**
     * Get occupyingClusterMemberId
     *
     * @return string 
     */
    public function getOccupyingClusterMemberId()
    {
        return $this->occupyingClusterMemberId;
    }
}
