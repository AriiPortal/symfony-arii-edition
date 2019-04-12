<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UjoHaProcess
 *
 * @ORM\Table(name="UJO_HA_PROCESS")
 * @ORM\Entity(readOnly=true,repositoryClass="Arii\ATSBundle\Entity\UjoHaProcessRepository")
 */
class UjoHaProcess
{
    /**
     * @var string
     *
     * @ORM\Column(name="HOSTNAME", type="string", length=255, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="UJO_MA_PROCESS_HOSTNAME_seq", allocationSize=1, initialValue=1)
     */
    private $hostname;

    /**
     * @var integer
     *
     * @ORM\Column(name="HA_DESIGNATOR_ID", type="integer", nullable=true)
     */
    private $haDesignatorId;

    /**
     * @var integer
     *
     * @ORM\Column(name="HA_STATUS_ID", type="integer", nullable=true)
     */
    private $haStatusId;

    /**
     * @var integer
     *
     * @ORM\Column(name="PID", type="integer", nullable=true)
     */
    private $pid;

    /**
     * @var integer
     *
     * @ORM\Column(name="PORT", type="integer", nullable=true)
     */
    private $port;

    /**
     * @var integer
     *
     * @ORM\Column(name="QUEUE_ID", type="integer", nullable=true)
     */
    private $queueId;

    /**
     * @var integer
     *
     * @ORM\Column(name="TIME_STAMP", type="integer", nullable=true)
     */
    private $timeStamp;



    /**
     * Get hostname
     *
     * @return string 
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Set haDesignatorId
     *
     * @param integer $haDesignatorId
     * @return UjoHaProcess
     */
    public function setHaDesignatorId($haDesignatorId)
    {
        $this->haDesignatorId = $haDesignatorId;

        return $this;
    }

    /**
     * Get haDesignatorId
     *
     * @return integer 
     */
    public function getHaDesignatorId()
    {
        return $this->haDesignatorId;
    }

    /**
     * Set haStatusId
     *
     * @param integer $haStatusId
     * @return UjoHaProcess
     */
    public function setHaStatusId($haStatusId)
    {
        $this->haStatusId = $haStatusId;

        return $this;
    }

    /**
     * Get haStatusId
     *
     * @return integer 
     */
    public function getHaStatusId()
    {
        return $this->haStatusId;
    }

    /**
     * Set pid
     *
     * @param integer $pid
     * @return UjoHaProcess
     */
    public function setPid($pid)
    {
        $this->pid = $pid;

        return $this;
    }

    /**
     * Get pid
     *
     * @return integer 
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Set port
     *
     * @param integer $port
     * @return UjoHaProcess
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get port
     *
     * @return integer 
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set queueId
     *
     * @param integer $queueId
     * @return UjoHaProcess
     */
    public function setQueueId($queueId)
    {
        $this->queueId = $queueId;

        return $this;
    }

    /**
     * Get queueId
     *
     * @return integer 
     */
    public function getQueueId()
    {
        return $this->queueId;
    }

    /**
     * Set timeStamp
     *
     * @param integer $timeStamp
     * @return UjoHaProcess
     */
    public function setTimeStamp($timeStamp)
    {
        $this->timeStamp = $timeStamp;

        return $this;
    }

    /**
     * Get timeStamp
     *
     * @return integer 
     */
    public function getTimeStamp()
    {
        return $this->timeStamp;
    }
}
