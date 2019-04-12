<?php

namespace Arii\JIDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SchedulerInstances
 *
 * @ORM\Table(name="scheduler_instances", indexes={@ORM\Index(name="SCHEDULER_INSTANCE_ID", columns={"SCHEDULER_ID"})})
 * @ORM\Entity(readOnly=true,repositoryClass="Arii\JIDBundle\Entity\SchedulerInstancesRepository")
 */
class SchedulerInstances
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
     * @ORM\Column(name="HOSTNAME", type="string", length=100, nullable=true)
     */
    private $hostname;

    /**
     * @var integer
     *
     * @ORM\Column(name="TCP_PORT", type="integer", nullable=true)
     */
    private $tcpPort;

    /**
     * @var integer
     *
     * @ORM\Column(name="UDP_PORT", type="integer", nullable=true)
     */
    private $udpPort;

    /**
     * @var string
     *
     * @ORM\Column(name="SUPERVISOR_HOSTNAME", type="string", length=100, nullable=true)
     */
    private $supervisorHostname;

    /**
     * @var integer
     *
     * @ORM\Column(name="SUPERVISOR_TCP_PORT", type="integer", nullable=true)
     */
    private $supervisorTcpPort;

    /**
     * @var integer
     *
     * @ORM\Column(name="JETTY_HTTP_PORT", type="integer", nullable=true)
     */
    private $jettyHttpPort;

    /**
     * @var integer
     *
     * @ORM\Column(name="JETTY_HTTPS_PORT", type="integer", nullable=true)
     */
    private $jettyHttpsPort;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="START_TIME", type="datetime", nullable=true)
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="STOP_TIME", type="datetime", nullable=true)
     */
    private $stopTime;

    /**
     * @var string
     *
     * @ORM\Column(name="DB_NAME", type="string", length=1000, nullable=true)
     */
    private $dbName;

    /**
     * @var string
     *
     * @ORM\Column(name="DB_HISTORY_TABLE_NAME", type="string", length=100, nullable=true)
     */
    private $dbHistoryTableName;

    /**
     * @var string
     *
     * @ORM\Column(name="DB_ORDER_HISTORY_TABLE_NAME", type="string", length=100, nullable=true)
     */
    private $dbOrderHistoryTableName;

    /**
     * @var string
     *
     * @ORM\Column(name="DB_ORDERS_TABLE_NAME", type="string", length=100, nullable=true)
     */
    private $dbOrdersTableName;

    /**
     * @var string
     *
     * @ORM\Column(name="DB_TASKS_TABLE_NAME", type="string", length=100, nullable=true)
     */
    private $dbTasksTableName;

    /**
     * @var string
     *
     * @ORM\Column(name="DB_VARIABLES_TABLE_NAME", type="string", length=100, nullable=true)
     */
    private $dbVariablesTableName;

    /**
     * @var string
     *
     * @ORM\Column(name="WORKING_DIRECTORY", type="string", length=250, nullable=true)
     */
    private $workingDirectory;

    /**
     * @var string
     *
     * @ORM\Column(name="LIVE_DIRECTORY", type="string", length=250, nullable=true)
     */
    private $liveDirectory;

    /**
     * @var string
     *
     * @ORM\Column(name="LOG_DIR", type="string", length=250, nullable=true)
     */
    private $logDir;

    /**
     * @var string
     *
     * @ORM\Column(name="INCLUDE_PATH", type="string", length=250, nullable=true)
     */
    private $includePath;

    /**
     * @var string
     *
     * @ORM\Column(name="INI_PATH", type="string", length=250, nullable=true)
     */
    private $iniPath;

    /**
     * @var integer
     *
     * @ORM\Column(name="IS_SERVICE", type="integer", nullable=false)
     */
    private $isService;

    /**
     * @var integer
     *
     * @ORM\Column(name="IS_RUNNING", type="integer", nullable=false)
     */
    private $isRunning;

    /**
     * @var integer
     *
     * @ORM\Column(name="IS_PAUSED", type="integer", nullable=false)
     */
    private $isPaused;

    /**
     * @var integer
     *
     * @ORM\Column(name="IS_CLUSTER", type="integer", nullable=false)
     */
    private $isCluster;

    /**
     * @var integer
     *
     * @ORM\Column(name="IS_AGENT", type="integer", nullable=false)
     */
    private $isAgent;

    /**
     * @var integer
     *
     * @ORM\Column(name="IS_SOS_COMMAND_WEBSERVICE", type="integer", nullable=false)
     */
    private $isSosCommandWebservice;

    /**
     * @var string
     *
     * @ORM\Column(name="PARAM", type="string", length=100, nullable=true)
     */
    private $param;

    /**
     * @var string
     *
     * @ORM\Column(name="RELEASE_NUMBER", type="string", length=100, nullable=true)
     */
    private $releaseNumber;



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
     * @return SchedulerInstances
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
     * @return SchedulerInstances
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
     * Set tcpPort
     *
     * @param integer $tcpPort
     * @return SchedulerInstances
     */
    public function setTcpPort($tcpPort)
    {
        $this->tcpPort = $tcpPort;

        return $this;
    }

    /**
     * Get tcpPort
     *
     * @return integer 
     */
    public function getTcpPort()
    {
        return $this->tcpPort;
    }

    /**
     * Set udpPort
     *
     * @param integer $udpPort
     * @return SchedulerInstances
     */
    public function setUdpPort($udpPort)
    {
        $this->udpPort = $udpPort;

        return $this;
    }

    /**
     * Get udpPort
     *
     * @return integer 
     */
    public function getUdpPort()
    {
        return $this->udpPort;
    }

    /**
     * Set supervisorHostname
     *
     * @param string $supervisorHostname
     * @return SchedulerInstances
     */
    public function setSupervisorHostname($supervisorHostname)
    {
        $this->supervisorHostname = $supervisorHostname;

        return $this;
    }

    /**
     * Get supervisorHostname
     *
     * @return string 
     */
    public function getSupervisorHostname()
    {
        return $this->supervisorHostname;
    }

    /**
     * Set supervisorTcpPort
     *
     * @param integer $supervisorTcpPort
     * @return SchedulerInstances
     */
    public function setSupervisorTcpPort($supervisorTcpPort)
    {
        $this->supervisorTcpPort = $supervisorTcpPort;

        return $this;
    }

    /**
     * Get supervisorTcpPort
     *
     * @return integer 
     */
    public function getSupervisorTcpPort()
    {
        return $this->supervisorTcpPort;
    }

    /**
     * Set jettyHttpPort
     *
     * @param integer $jettyHttpPort
     * @return SchedulerInstances
     */
    public function setJettyHttpPort($jettyHttpPort)
    {
        $this->jettyHttpPort = $jettyHttpPort;

        return $this;
    }

    /**
     * Get jettyHttpPort
     *
     * @return integer 
     */
    public function getJettyHttpPort()
    {
        return $this->jettyHttpPort;
    }

    /**
     * Set jettyHttpsPort
     *
     * @param integer $jettyHttpsPort
     * @return SchedulerInstances
     */
    public function setJettyHttpsPort($jettyHttpsPort)
    {
        $this->jettyHttpsPort = $jettyHttpsPort;

        return $this;
    }

    /**
     * Get jettyHttpsPort
     *
     * @return integer 
     */
    public function getJettyHttpsPort()
    {
        return $this->jettyHttpsPort;
    }

    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     * @return SchedulerInstances
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
     * Set stopTime
     *
     * @param \DateTime $stopTime
     * @return SchedulerInstances
     */
    public function setStopTime($stopTime)
    {
        $this->stopTime = $stopTime;

        return $this;
    }

    /**
     * Get stopTime
     *
     * @return \DateTime 
     */
    public function getStopTime()
    {
        return $this->stopTime;
    }

    /**
     * Set dbName
     *
     * @param string $dbName
     * @return SchedulerInstances
     */
    public function setDbName($dbName)
    {
        $this->dbName = $dbName;

        return $this;
    }

    /**
     * Get dbName
     *
     * @return string 
     */
    public function getDbName()
    {
        return $this->dbName;
    }

    /**
     * Set dbHistoryTableName
     *
     * @param string $dbHistoryTableName
     * @return SchedulerInstances
     */
    public function setDbHistoryTableName($dbHistoryTableName)
    {
        $this->dbHistoryTableName = $dbHistoryTableName;

        return $this;
    }

    /**
     * Get dbHistoryTableName
     *
     * @return string 
     */
    public function getDbHistoryTableName()
    {
        return $this->dbHistoryTableName;
    }

    /**
     * Set dbOrderHistoryTableName
     *
     * @param string $dbOrderHistoryTableName
     * @return SchedulerInstances
     */
    public function setDbOrderHistoryTableName($dbOrderHistoryTableName)
    {
        $this->dbOrderHistoryTableName = $dbOrderHistoryTableName;

        return $this;
    }

    /**
     * Get dbOrderHistoryTableName
     *
     * @return string 
     */
    public function getDbOrderHistoryTableName()
    {
        return $this->dbOrderHistoryTableName;
    }

    /**
     * Set dbOrdersTableName
     *
     * @param string $dbOrdersTableName
     * @return SchedulerInstances
     */
    public function setDbOrdersTableName($dbOrdersTableName)
    {
        $this->dbOrdersTableName = $dbOrdersTableName;

        return $this;
    }

    /**
     * Get dbOrdersTableName
     *
     * @return string 
     */
    public function getDbOrdersTableName()
    {
        return $this->dbOrdersTableName;
    }

    /**
     * Set dbTasksTableName
     *
     * @param string $dbTasksTableName
     * @return SchedulerInstances
     */
    public function setDbTasksTableName($dbTasksTableName)
    {
        $this->dbTasksTableName = $dbTasksTableName;

        return $this;
    }

    /**
     * Get dbTasksTableName
     *
     * @return string 
     */
    public function getDbTasksTableName()
    {
        return $this->dbTasksTableName;
    }

    /**
     * Set dbVariablesTableName
     *
     * @param string $dbVariablesTableName
     * @return SchedulerInstances
     */
    public function setDbVariablesTableName($dbVariablesTableName)
    {
        $this->dbVariablesTableName = $dbVariablesTableName;

        return $this;
    }

    /**
     * Get dbVariablesTableName
     *
     * @return string 
     */
    public function getDbVariablesTableName()
    {
        return $this->dbVariablesTableName;
    }

    /**
     * Set workingDirectory
     *
     * @param string $workingDirectory
     * @return SchedulerInstances
     */
    public function setWorkingDirectory($workingDirectory)
    {
        $this->workingDirectory = $workingDirectory;

        return $this;
    }

    /**
     * Get workingDirectory
     *
     * @return string 
     */
    public function getWorkingDirectory()
    {
        return $this->workingDirectory;
    }

    /**
     * Set liveDirectory
     *
     * @param string $liveDirectory
     * @return SchedulerInstances
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
     * Set logDir
     *
     * @param string $logDir
     * @return SchedulerInstances
     */
    public function setLogDir($logDir)
    {
        $this->logDir = $logDir;

        return $this;
    }

    /**
     * Get logDir
     *
     * @return string 
     */
    public function getLogDir()
    {
        return $this->logDir;
    }

    /**
     * Set includePath
     *
     * @param string $includePath
     * @return SchedulerInstances
     */
    public function setIncludePath($includePath)
    {
        $this->includePath = $includePath;

        return $this;
    }

    /**
     * Get includePath
     *
     * @return string 
     */
    public function getIncludePath()
    {
        return $this->includePath;
    }

    /**
     * Set iniPath
     *
     * @param string $iniPath
     * @return SchedulerInstances
     */
    public function setIniPath($iniPath)
    {
        $this->iniPath = $iniPath;

        return $this;
    }

    /**
     * Get iniPath
     *
     * @return string 
     */
    public function getIniPath()
    {
        return $this->iniPath;
    }

    /**
     * Set isService
     *
     * @param integer $isService
     * @return SchedulerInstances
     */
    public function setIsService($isService)
    {
        $this->isService = $isService;

        return $this;
    }

    /**
     * Get isService
     *
     * @return integer 
     */
    public function getIsService()
    {
        return $this->isService;
    }

    /**
     * Set isRunning
     *
     * @param integer $isRunning
     * @return SchedulerInstances
     */
    public function setIsRunning($isRunning)
    {
        $this->isRunning = $isRunning;

        return $this;
    }

    /**
     * Get isRunning
     *
     * @return integer 
     */
    public function getIsRunning()
    {
        return $this->isRunning;
    }

    /**
     * Set isPaused
     *
     * @param integer $isPaused
     * @return SchedulerInstances
     */
    public function setIsPaused($isPaused)
    {
        $this->isPaused = $isPaused;

        return $this;
    }

    /**
     * Get isPaused
     *
     * @return integer 
     */
    public function getIsPaused()
    {
        return $this->isPaused;
    }

    /**
     * Set isCluster
     *
     * @param integer $isCluster
     * @return SchedulerInstances
     */
    public function setIsCluster($isCluster)
    {
        $this->isCluster = $isCluster;

        return $this;
    }

    /**
     * Get isCluster
     *
     * @return integer 
     */
    public function getIsCluster()
    {
        return $this->isCluster;
    }

    /**
     * Set isAgent
     *
     * @param integer $isAgent
     * @return SchedulerInstances
     */
    public function setIsAgent($isAgent)
    {
        $this->isAgent = $isAgent;

        return $this;
    }

    /**
     * Get isAgent
     *
     * @return integer 
     */
    public function getIsAgent()
    {
        return $this->isAgent;
    }

    /**
     * Set isSosCommandWebservice
     *
     * @param integer $isSosCommandWebservice
     * @return SchedulerInstances
     */
    public function setIsSosCommandWebservice($isSosCommandWebservice)
    {
        $this->isSosCommandWebservice = $isSosCommandWebservice;

        return $this;
    }

    /**
     * Get isSosCommandWebservice
     *
     * @return integer 
     */
    public function getIsSosCommandWebservice()
    {
        return $this->isSosCommandWebservice;
    }

    /**
     * Set param
     *
     * @param string $param
     * @return SchedulerInstances
     */
    public function setParam($param)
    {
        $this->param = $param;

        return $this;
    }

    /**
     * Get param
     *
     * @return string 
     */
    public function getParam()
    {
        return $this->param;
    }

    /**
     * Set releaseNumber
     *
     * @param string $releaseNumber
     * @return SchedulerInstances
     */
    public function setReleaseNumber($releaseNumber)
    {
        $this->releaseNumber = $releaseNumber;

        return $this;
    }

    /**
     * Get releaseNumber
     *
     * @return string 
     */
    public function getReleaseNumber()
    {
        return $this->releaseNumber;
    }
}
