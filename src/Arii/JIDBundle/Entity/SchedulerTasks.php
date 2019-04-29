<?php

namespace Arii\JIDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SchedulerTasks
 *
 * @ORM\Table(name="scheduler_tasks")
 * @ORM\Entity(readOnly=true,repositoryClass="Arii\JIDBundle\Entity\SchedulerTasksRepository")
 */
class SchedulerTasks
{
    /**
     * @var integer
     *
     * @ORM\Column(name="TASK_ID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $taskId;

    /**
     * @var string
     *
     * @ORM\Column(name="SPOOLER_ID", type="string", length=100, nullable=false)
     */
    private $spoolerId;

    /**
     * @var string
     *
     * @ORM\Column(name="CLUSTER_MEMBER_ID", type="string", length=100, nullable=true)
     */
    private $clusterMemberId;

    /**
     * @var string
     *
     * @ORM\Column(name="JOB_NAME", type="string", length=255, nullable=false)
     */
    private $jobName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ENQUEUE_TIME", type="datetime", nullable=true)
     */
    private $enqueueTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="START_AT_TIME", type="datetime", nullable=true)
     */
    private $startAtTime;

    /**
     * @var string
     *
     * @ORM\Column(name="PARAMETERS", type="text", nullable=true)
     */
    private $parameters;

    /**
     * @var string
     *
     * @ORM\Column(name="TASK_XML", type="text", nullable=true)
     */
    private $taskXml;



    /**
     * Get taskId
     *
     * @return integer 
     */
    public function getTaskId()
    {
        return $this->taskId;
    }

    /**
     * Set spoolerId
     *
     * @param string $spoolerId
     * @return SchedulerTasks
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
     * Set clusterMemberId
     *
     * @param string $clusterMemberId
     * @return SchedulerTasks
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
     * @return SchedulerTasks
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
     * Set enqueueTime
     *
     * @param \DateTime $enqueueTime
     * @return SchedulerTasks
     */
    public function setEnqueueTime($enqueueTime)
    {
        $this->enqueueTime = $enqueueTime;

        return $this;
    }

    /**
     * Get enqueueTime
     *
     * @return \DateTime 
     */
    public function getEnqueueTime()
    {
        return $this->enqueueTime;
    }

    /**
     * Set startAtTime
     *
     * @param \DateTime $startAtTime
     * @return SchedulerTasks
     */
    public function setStartAtTime($startAtTime)
    {
        $this->startAtTime = $startAtTime;

        return $this;
    }

    /**
     * Get startAtTime
     *
     * @return \DateTime 
     */
    public function getStartAtTime()
    {
        return $this->startAtTime;
    }

    /**
     * Set parameters
     *
     * @param string $parameters
     * @return SchedulerTasks
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
     * Set taskXml
     *
     * @param string $taskXml
     * @return SchedulerTasks
     */
    public function setTaskXml($taskXml)
    {
        $this->taskXml = $taskXml;

        return $this;
    }

    /**
     * Get taskXml
     *
     * @return string 
     */
    public function getTaskXml()
    {
        return $this->taskXml;
    }
}
