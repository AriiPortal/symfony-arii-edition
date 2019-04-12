<?php

namespace Arii\JIDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SchedulerClusters
 *
 * @ORM\Table(name="scheduler_clusters")
 * @ORM\Entity(readOnly=true,repositoryClass="Arii\JIDBundle\Entity\SchedulerClustersRepository")
 */
class SchedulerClusters
{
    /**
     * @var string
     *
     * @ORM\Column(name="MEMBER_ID", type="string", length=100, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $memberId;

    /**
     * @var string
     *
     * @ORM\Column(name="SCHEDULER_ID", type="string", length=100, nullable=false)
     */
    private $schedulerId;

    /**
     * @var integer
     *
     * @ORM\Column(name="PRECEDENCE", type="integer", nullable=true)
     */
    private $precedence;

    /**
     * @var integer
     *
     * @ORM\Column(name="LAST_HEART_BEAT", type="integer", nullable=true)
     */
    private $lastHeartBeat;

    /**
     * @var integer
     *
     * @ORM\Column(name="NEXT_HEART_BEAT", type="integer", nullable=true)
     */
    private $nextHeartBeat;

    /**
     * @var integer
     *
     * @ORM\Column(name="ACTIVE", type="integer", nullable=true)
     */
    private $active;

    /**
     * @var integer
     *
     * @ORM\Column(name="EXCLUSIVE", type="integer", nullable=true)
     */
    private $exclusive;

    /**
     * @var integer
     *
     * @ORM\Column(name="DEAD", type="integer", nullable=true)
     */
    private $dead;

    /**
     * @var string
     *
     * @ORM\Column(name="COMMAND", type="string", length=250, nullable=true)
     */
    private $command;

    /**
     * @var string
     *
     * @ORM\Column(name="HTTP_URL", type="string", length=100, nullable=true)
     */
    private $httpUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="DEACTIVATING_MEMBER_ID", type="string", length=100, nullable=true)
     */
    private $deactivatingMemberId;

    /**
     * @var string
     *
     * @ORM\Column(name="XML", type="text", nullable=true)
     */
    private $xml;

    /**
     * @var integer
     *
     * @ORM\Column(name="PAUSED", type="integer", nullable=true)
     */
    private $paused;



    /**
     * Get memberId
     *
     * @return string 
     */
    public function getMemberId()
    {
        return $this->memberId;
    }

    /**
     * Set schedulerId
     *
     * @param string $schedulerId
     * @return SchedulerClusters
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
     * Set precedence
     *
     * @param integer $precedence
     * @return SchedulerClusters
     */
    public function setPrecedence($precedence)
    {
        $this->precedence = $precedence;

        return $this;
    }

    /**
     * Get precedence
     *
     * @return integer 
     */
    public function getPrecedence()
    {
        return $this->precedence;
    }

    /**
     * Set lastHeartBeat
     *
     * @param integer $lastHeartBeat
     * @return SchedulerClusters
     */
    public function setLastHeartBeat($lastHeartBeat)
    {
        $this->lastHeartBeat = $lastHeartBeat;

        return $this;
    }

    /**
     * Get lastHeartBeat
     *
     * @return integer 
     */
    public function getLastHeartBeat()
    {
        return $this->lastHeartBeat;
    }

    /**
     * Set nextHeartBeat
     *
     * @param integer $nextHeartBeat
     * @return SchedulerClusters
     */
    public function setNextHeartBeat($nextHeartBeat)
    {
        $this->nextHeartBeat = $nextHeartBeat;

        return $this;
    }

    /**
     * Get nextHeartBeat
     *
     * @return integer 
     */
    public function getNextHeartBeat()
    {
        return $this->nextHeartBeat;
    }

    /**
     * Set active
     *
     * @param integer $active
     * @return SchedulerClusters
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return integer 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set exclusive
     *
     * @param integer $exclusive
     * @return SchedulerClusters
     */
    public function setExclusive($exclusive)
    {
        $this->exclusive = $exclusive;

        return $this;
    }

    /**
     * Get exclusive
     *
     * @return integer 
     */
    public function getExclusive()
    {
        return $this->exclusive;
    }

    /**
     * Set dead
     *
     * @param integer $dead
     * @return SchedulerClusters
     */
    public function setDead($dead)
    {
        $this->dead = $dead;

        return $this;
    }

    /**
     * Get dead
     *
     * @return integer 
     */
    public function getDead()
    {
        return $this->dead;
    }

    /**
     * Set command
     *
     * @param string $command
     * @return SchedulerClusters
     */
    public function setCommand($command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Get command
     *
     * @return string 
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Set httpUrl
     *
     * @param string $httpUrl
     * @return SchedulerClusters
     */
    public function setHttpUrl($httpUrl)
    {
        $this->httpUrl = $httpUrl;

        return $this;
    }

    /**
     * Get httpUrl
     *
     * @return string 
     */
    public function getHttpUrl()
    {
        return $this->httpUrl;
    }

    /**
     * Set deactivatingMemberId
     *
     * @param string $deactivatingMemberId
     * @return SchedulerClusters
     */
    public function setDeactivatingMemberId($deactivatingMemberId)
    {
        $this->deactivatingMemberId = $deactivatingMemberId;

        return $this;
    }

    /**
     * Get deactivatingMemberId
     *
     * @return string 
     */
    public function getDeactivatingMemberId()
    {
        return $this->deactivatingMemberId;
    }

    /**
     * Set xml
     *
     * @param string $xml
     * @return SchedulerClusters
     */
    public function setXml($xml)
    {
        $this->xml = $xml;

        return $this;
    }

    /**
     * Get xml
     *
     * @return string 
     */
    public function getXml()
    {
        return $this->xml;
    }

    /**
     * Set paused
     *
     * @param integer $paused
     * @return SchedulerClusters
     */
    public function setPaused($paused)
    {
        $this->paused = $paused;

        return $this;
    }

    /**
     * Get paused
     *
     * @return integer 
     */
    public function getPaused()
    {
        return $this->paused;
    }
}
