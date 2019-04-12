<?php

namespace Arii\JIDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InventoryInstances
 *
 * @ORM\Table(name="inventory_instances", uniqueConstraints={@ORM\UniqueConstraint(name="INVENTORY_II_UNIQUE", columns={"SCHEDULER_ID", "HOSTNAME", "PORT"})}, indexes={@ORM\Index(name="REPORTING_II_SCHEDULER_ID", columns={"SCHEDULER_ID"}), @ORM\Index(name="REPORTING_II_HOST", columns={"HOSTNAME", "PORT"})})
 * @ORM\Entity(readOnly=true,repositoryClass="Arii\JIDBundle\Entity\InventoryInstancesRepository")
 */
class InventoryInstances
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
     * @ORM\Column(name="HOSTNAME", type="string", length=100, nullable=false)
     */
    private $hostname;

    /**
     * @var integer
     *
     * @ORM\Column(name="PORT", type="integer", nullable=false)
     */
    private $port;

    /**
     * @var string
     *
     * @ORM\Column(name="LIVE_DIRECTORY", type="string", length=255, nullable=false)
     */
    private $liveDirectory;

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
     * @var integer
     *
     * @ORM\Column(name="OS_ID", type="integer", nullable=false)
     */
    private $osId;

    /**
     * @var string
     *
     * @ORM\Column(name="VERSION", type="string", length=30, nullable=false)
     */
    private $version;

    /**
     * @var string
     *
     * @ORM\Column(name="COMMAND_URL", type="string", length=100, nullable=false)
     */
    private $commandUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="URL", type="string", length=100, nullable=false)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="TIMEZONE", type="string", length=30, nullable=false)
     */
    private $timezone;

    /**
     * @var string
     *
     * @ORM\Column(name="CLUSTER_TYPE", type="string", length=30, nullable=false)
     */
    private $clusterType;

    /**
     * @var integer
     *
     * @ORM\Column(name="PRECEDENCE", type="integer", nullable=true)
     */
    private $precedence;

    /**
     * @var string
     *
     * @ORM\Column(name="DBMS_NAME", type="string", length=30, nullable=false)
     */
    private $dbmsName;

    /**
     * @var string
     *
     * @ORM\Column(name="DBMS_VERSION", type="string", length=100, nullable=true)
     */
    private $dbmsVersion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="STARTED_AT", type="datetime", nullable=true)
     */
    private $startedAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="SUPERVISOR_ID", type="integer", nullable=true)
     */
    private $supervisorId;

    /**
     * @var string
     *
     * @ORM\Column(name="AUTH", type="string", length=255, nullable=true)
     */
    private $auth;



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
     * @return InventoryInstances
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
     * Set hostname
     *
     * @param string $hostname
     * @return InventoryInstances
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;

        return $this;
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
     * Set port
     *
     * @param integer $port
     * @return InventoryInstances
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
     * Set liveDirectory
     *
     * @param string $liveDirectory
     * @return InventoryInstances
     */
    public function setLiveDirectory($liveDirectory)
    {
        $this->liveDirectory = $liveDirectory;

        return $this;
    }

    /**
     * Get liveDirectory
     *
     * @return string 
     */
    public function getLiveDirectory()
    {
        return $this->liveDirectory;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return InventoryInstances
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
     * @return InventoryInstances
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

    /**
     * Set osId
     *
     * @param integer $osId
     * @return InventoryInstances
     */
    public function setOsId($osId)
    {
        $this->osId = $osId;

        return $this;
    }

    /**
     * Get osId
     *
     * @return integer 
     */
    public function getOsId()
    {
        return $this->osId;
    }

    /**
     * Set version
     *
     * @param string $version
     * @return InventoryInstances
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return string 
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set commandUrl
     *
     * @param string $commandUrl
     * @return InventoryInstances
     */
    public function setCommandUrl($commandUrl)
    {
        $this->commandUrl = $commandUrl;

        return $this;
    }

    /**
     * Get commandUrl
     *
     * @return string 
     */
    public function getCommandUrl()
    {
        return $this->commandUrl;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return InventoryInstances
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set timezone
     *
     * @param string $timezone
     * @return InventoryInstances
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Get timezone
     *
     * @return string 
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Set clusterType
     *
     * @param string $clusterType
     * @return InventoryInstances
     */
    public function setClusterType($clusterType)
    {
        $this->clusterType = $clusterType;

        return $this;
    }

    /**
     * Get clusterType
     *
     * @return string 
     */
    public function getClusterType()
    {
        return $this->clusterType;
    }

    /**
     * Set precedence
     *
     * @param integer $precedence
     * @return InventoryInstances
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
     * Set dbmsName
     *
     * @param string $dbmsName
     * @return InventoryInstances
     */
    public function setDbmsName($dbmsName)
    {
        $this->dbmsName = $dbmsName;

        return $this;
    }

    /**
     * Get dbmsName
     *
     * @return string 
     */
    public function getDbmsName()
    {
        return $this->dbmsName;
    }

    /**
     * Set dbmsVersion
     *
     * @param string $dbmsVersion
     * @return InventoryInstances
     */
    public function setDbmsVersion($dbmsVersion)
    {
        $this->dbmsVersion = $dbmsVersion;

        return $this;
    }

    /**
     * Get dbmsVersion
     *
     * @return string 
     */
    public function getDbmsVersion()
    {
        return $this->dbmsVersion;
    }

    /**
     * Set startedAt
     *
     * @param \DateTime $startedAt
     * @return InventoryInstances
     */
    public function setStartedAt($startedAt)
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * Get startedAt
     *
     * @return \DateTime 
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * Set supervisorId
     *
     * @param integer $supervisorId
     * @return InventoryInstances
     */
    public function setSupervisorId($supervisorId)
    {
        $this->supervisorId = $supervisorId;

        return $this;
    }

    /**
     * Get supervisorId
     *
     * @return integer 
     */
    public function getSupervisorId()
    {
        return $this->supervisorId;
    }

    /**
     * Set auth
     *
     * @param string $auth
     * @return InventoryInstances
     */
    public function setAuth($auth)
    {
        $this->auth = $auth;

        return $this;
    }

    /**
     * Get auth
     *
     * @return string 
     */
    public function getAuth()
    {
        return $this->auth;
    }
}
