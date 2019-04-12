<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UjoMaProcess
 *
 * @ORM\Table(name="UJO_MA_PROCESS")
 * @ORM\Entity(readOnly=true,repositoryClass="Arii\ATSBundle\Entity\UjoMaProcessRepository")
 */
class UjoMaProcess
{
    /**
     * @var string
     *
     * @ORM\Column(name="COMM_ALIAS", type="string", length=16, nullable=true)
     */
    private $commAlias;

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
     * @ORM\Column(name="MA_DESIGNATOR_ID", type="integer", nullable=true)
     */
    private $maDesignatorId;

    /**
     * @var integer
     *
     * @ORM\Column(name="MA_STATUS_ID", type="integer", nullable=true)
     */
    private $maStatusId;

    /**
     * @var string
     *
     * @ORM\Column(name="MGR_ALIAS", type="string", length=255, nullable=true)
     */
    private $mgrAlias;

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
     * Set commAlias
     *
     * @param string $commAlias
     * @return UjoMaProcess
     */
    public function setCommAlias($commAlias)
    {
        $this->commAlias = $commAlias;

        return $this;
    }

    /**
     * Get commAlias
     *
     * @return string 
     */
    public function getCommAlias()
    {
        return $this->commAlias;
    }

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
     * Set maDesignatorId
     *
     * @param integer $maDesignatorId
     * @return UjoMaProcess
     */
    public function setMaDesignatorId($maDesignatorId)
    {
        $this->maDesignatorId = $maDesignatorId;

        return $this;
    }

    /**
     * Get maDesignatorId
     *
     * @return integer 
     */
    public function getMaDesignatorId()
    {
        return $this->maDesignatorId;
    }

    /**
     * Set maStatusId
     *
     * @param integer $maStatusId
     * @return UjoMaProcess
     */
    public function setMaStatusId($maStatusId)
    {
        $this->maStatusId = $maStatusId;

        return $this;
    }

    /**
     * Get maStatusId
     *
     * @return integer 
     */
    public function getMaStatusId()
    {
        return $this->maStatusId;
    }

    /**
     * Set mgrAlias
     *
     * @param string $mgrAlias
     * @return UjoMaProcess
     */
    public function setMgrAlias($mgrAlias)
    {
        $this->mgrAlias = $mgrAlias;

        return $this;
    }

    /**
     * Get mgrAlias
     *
     * @return string 
     */
    public function getMgrAlias()
    {
        return $this->mgrAlias;
    }

    /**
     * Set pid
     *
     * @param integer $pid
     * @return UjoMaProcess
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
     * @return UjoMaProcess
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
     * @return UjoMaProcess
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
     * @return UjoMaProcess
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
