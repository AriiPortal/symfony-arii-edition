<?php

namespace Arii\JOCBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * state_orders
 *
 * @ORM\Table(name="JOC_ORDERS")
 * @ORM\Entity(repositoryClass="Arii\JOCBundle\Entity\OrdersRepository")
 */
class Orders
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
     * 
     * @ORM\ManyToOne(targetEntity="JobChainNodes")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $job_chain_node;

    /**
     * @var string
     *
     * @ORM\Column(name="id_order", type="string", length=255 , nullable=true )
     */
    private $id_order;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="spooler_id", type="integer", nullable=true )
     */
    private $spooler_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="job_chain_id", type="integer", nullable=true )
     */
    private $job_chain_id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=100, nullable=true )
     */
    private $state;

    /**
     * @var integer
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true )
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true )
     */
    private $path;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=1024, nullable=true )
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="state_text", type="string", length=1024, nullable=true)
     */
    private $state_text;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="next_start_time", type="datetime", nullable=true)
     */
    private $next_start_time;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="setback", type="datetime", nullable=true)
     */
    private $setback;

    /**
     * @var integer
     *
     * @ORM\Column(name="setback_count", type="integer", nullable=true)
     */
    private $setback_count;

    /**
     * @var string
     *
     * @ORM\Column(name="initial_state", type="string", length=100)
     */
    private $initial_state;
    /**
     * @var string
     *
     * @ORM\Column(name="end_state", type="string", length=100, nullable=true)
     */
    private $end_state;

    /**
     * @var integer
     *
     * @ORM\Column(name="priority", type="integer", nullable=true)
     */
    private $priority;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_time", type="datetime", nullable=true)
     */
    private $start_time;

    /**
     * @var integer
     *
     * @ORM\Column(name="suspended", type="boolean")
     */
    private $suspended;

    /**
     * @var integer
     *
     * @ORM\Column(name="running", type="boolean")
     */
    private $running;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="history_id", type="integer", nullable=true)
     */
    private $history_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="task_id", type="integer", nullable=true)
     */
    private $task_id;

    /**
     * @var string
     *
     * @ORM\Column(name="schedule", type="string", length=255, nullable=true)
     */
    private $schedule;

    /**
     * @var string
     *
     * @ORM\Column(name="in_process_since", type="time", nullable=true)
     */
    private $in_process_since;

    /**
     * @var string
     *
     * @ORM\Column(name="touched", type="boolean" )
     */
    private $touched;

    /**
     * @var string
     *
     * @ORM\Column(name="on_blacklist", type="boolean" )
     */
    private $on_blacklist;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_write_time", type="datetime", nullable=true)
     */
    private $last_write_time;

    /**
     * @var datetime
     *
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;
    

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
     * Set id_order
     *
     * @param string $idOrder
     * @return Orders
     */
    public function setIdOrder($idOrder)
    {
        $this->id_order = $idOrder;

        return $this;
    }

    /**
     * Get id_order
     *
     * @return string 
     */
    public function getIdOrder()
    {
        return $this->id_order;
    }

    /**
     * Set spooler_id
     *
     * @param integer $spoolerId
     * @return Orders
     */
    public function setSpoolerId($spoolerId)
    {
        $this->spooler_id = $spoolerId;

        return $this;
    }

    /**
     * Get spooler_id
     *
     * @return integer 
     */
    public function getSpoolerId()
    {
        return $this->spooler_id;
    }

    /**
     * Set job_chain_id
     *
     * @param integer $$jobChainId
     * @return Orders
     */
    public function setJobChainId($jobChainId)
    {
        $this->job_chain_id = $jobChainId;

        return $this;
    }

    /**
     * Get job_chain_id
     *
     * @return integer 
     */
    public function getJobChainId()
    {
        return $this->job_chain_id;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return Orders
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
     * Set name
     *
     * @param string $name
     * @return Orders
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Orders
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Orders
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
     * Set state_text
     *
     * @param string $stateText
     * @return Orders
     */
    public function setStateText($stateText)
    {
        $this->state_text = $stateText;

        return $this;
    }

    /**
     * Get state_text
     *
     * @return string 
     */
    public function getStateText()
    {
        return $this->state_text;
    }

    /**
     * Set next_start_time
     *
     * @param \DateTime $nextStartTime
     * @return Orders
     */
    public function setNextStartTime($nextStartTime)
    {
        $this->next_start_time = $nextStartTime;

        return $this;
    }

    /**
     * Get next_start_time
     *
     * @return \DateTime 
     */
    public function getNextStartTime()
    {
        return $this->next_start_time;
    }

    /**
     * Set setback
     *
     * @param \DateTime $setback
     * @return Orders
     */
    public function setSetback($setback)
    {
        $this->setback = $setback;

        return $this;
    }

    /**
     * Get setback
     *
     * @return \DateTime 
     */
    public function getSetback()
    {
        return $this->setback;
    }

    /**
     * Set setback_count
     *
     * @param integer $setbackCount
     * @return Orders
     */
    public function setSetbackCount($setbackCount)
    {
        $this->setback_count = $setbackCount;

        return $this;
    }

    /**
     * Get setback_count
     *
     * @return integer 
     */
    public function getSetbackCount()
    {
        return $this->setback_count;
    }

    /**
     * Set initial_state
     *
     * @param string $initialState
     * @return Orders
     */
    public function setInitialState($initialState)
    {
        $this->initial_state = $initialState;

        return $this;
    }

    /**
     * Get initial_state
     *
     * @return string 
     */
    public function getInitialState()
    {
        return $this->initial_state;
    }

    /**
     * Set end_state
     *
     * @param string $endState
     * @return Orders
     */
    public function setEndState($endState)
    {
        $this->end_state = $endState;

        return $this;
    }

    /**
     * Get end_state
     *
     * @return string 
     */
    public function getEndState()
    {
        return $this->end_state;
    }

    /**
     * Set priority
     *
     * @param integer $priority
     * @return Orders
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
     * Set created
     *
     * @param \DateTime $created
     * @return Orders
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
     * Set start_time
     *
     * @param \DateTime $startTime
     * @return Orders
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
     * Set suspended
     *
     * @param boolean $suspended
     * @return Orders
     */
    public function setSuspended($suspended)
    {
        $this->suspended = $suspended;

        return $this;
    }

    /**
     * Get suspended
     *
     * @return boolean 
     */
    public function getSuspended()
    {
        return $this->suspended;
    }

    /**
     * Set history_id
     *
     * @param integer $historyId
     * @return Orders
     */
    public function setHistoryId($historyId)
    {
        $this->history_id = $historyId;

        return $this;
    }

    /**
     * Get history_id
     *
     * @return integer 
     */
    public function getHistoryId()
    {
        return $this->history_id;
    }

    /**
     * Set task_id
     *
     * @param integer $taskId
     * @return Orders
     */
    public function setTaskId($taskId)
    {
        $this->task_id = $taskId;

        return $this;
    }

    /**
     * Get task_id
     *
     * @return integer 
     */
    public function getTaskId()
    {
        return $this->task_id;
    }

    /**
     * Set schedule
     *
     * @param string $schedule
     * @return Orders
     */
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * Get schedule
     *
     * @return string 
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * Set in_process_since
     *
     * @param \DateTime $inProcessSince
     * @return Orders
     */
    public function setInProcessSince($inProcessSince)
    {
        $this->in_process_since = $inProcessSince;

        return $this;
    }

    /**
     * Get in_process_since
     *
     * @return \DateTime 
     */
    public function getInProcessSince()
    {
        return $this->in_process_since;
    }

    /**
     * Set touched
     *
     * @param boolean $touched
     * @return Orders
     */
    public function setTouched($touched)
    {
        $this->touched = $touched;

        return $this;
    }

    /**
     * Get touched
     *
     * @return boolean 
     */
    public function getTouched()
    {
        return $this->touched;
    }

    /**
     * Set on_blacklist
     *
     * @param boolean $onBlacklist
     * @return Orders
     */
    public function setOnBlacklist($onBlacklist)
    {
        $this->on_blacklist = $onBlacklist;

        return $this;
    }

    /**
     * Get on_blacklist
     *
     * @return boolean 
     */
    public function getOnBlacklist()
    {
        return $this->on_blacklist;
    }

    /**
     * Set last_write_time
     *
     * @param \DateTime $lastWriteTime
     * @return Orders
     */
    public function setLastWriteTime($lastWriteTime)
    {
        $this->last_write_time = $lastWriteTime;

        return $this;
    }

    /**
     * Get last_write_time
     *
     * @return \DateTime 
     */
    public function getLastWriteTime()
    {
        return $this->last_write_time;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Orders
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
     * Set job_chain_node
     *
     * @param \Arii\JOCBundle\Entity\JobChainNodes $jobChainNode
     * @return Orders
     */
    public function setJobChainNode(\Arii\JOCBundle\Entity\JobChainNodes $jobChainNode = null)
    {
        $this->job_chain_node = $jobChainNode;

        return $this;
    }

    /**
     * Get job_chain_node
     *
     * @return \Arii\JOCBundle\Entity\JobChainNodes 
     */
    public function getJobChainNode()
    {
        return $this->job_chain_node;
    }

    /**
     * Set running
     *
     * @param boolean $running
     * @return Orders
     */
    public function setRunning($running)
    {
        $this->running = $running;

        return $this;
    }

    /**
     * Get running
     *
     * @return boolean 
     */
    public function getRunning()
    {
        return $this->running;
    }
}
