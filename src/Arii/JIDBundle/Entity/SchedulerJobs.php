<?php

namespace Arii\JIDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SchedulerJobs
 *
 * @ORM\Table(name="scheduler_jobs")
 * @ORM\Entity(readOnly=true)
 */
class SchedulerJobs
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
     * @ORM\Column(name="CLUSTER_MEMBER_ID", type="string", length=100, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $clusterMemberId;

    /**
     * @var string
     *
     * @ORM\Column(name="PATH", type="string", length=255, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $path;

    /**
     * @var integer
     *
     * @ORM\Column(name="STOPPED", type="integer", nullable=false)
     */
    private $stopped;

    /**
     * @var string
     *
     * @ORM\Column(name="NEXT_START_TIME", type="string", length=24, nullable=true)
     */
    private $nextStartTime;



    /**
     * Set spoolerId
     *
     * @param string $spoolerId
     * @return SchedulerJobs
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
     * @return SchedulerJobs
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
     * Set path
     *
     * @param string $path
     * @return SchedulerJobs
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
     * Set stopped
     *
     * @param integer $stopped
     * @return SchedulerJobs
     */
    public function setStopped($stopped)
    {
        $this->stopped = $stopped;

        return $this;
    }

    /**
     * Get stopped
     *
     * @return integer 
     */
    public function getStopped()
    {
        return $this->stopped;
    }

    /**
     * Set nextStartTime
     *
     * @param string $nextStartTime
     * @return SchedulerJobs
     */
    public function setNextStartTime($nextStartTime)
    {
        $this->nextStartTime = $nextStartTime;

        return $this;
    }

    /**
     * Get nextStartTime
     *
     * @return string 
     */
    public function getNextStartTime()
    {
        return $this->nextStartTime;
    }
}
