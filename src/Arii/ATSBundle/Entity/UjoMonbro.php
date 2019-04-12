<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UjoMonbro
 *
 * @ORM\Table(name="UJO_MONBRO")
 * @ORM\Entity
 */
class UjoMonbro
{
    /**
     * @var string
     *
     * @ORM\Column(name="AFTER_TIME", type="string", length=20, nullable=true)
     */
    private $afterTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="ALARM", type="smallint", nullable=true)
     */
    private $alarm;

    /**
     * @var integer
     *
     * @ORM\Column(name="ALARM_VERIF", type="smallint", nullable=true)
     */
    private $alarmVerif;

    /**
     * @var integer
     *
     * @ORM\Column(name="ALL_EVENTS", type="smallint", nullable=true)
     */
    private $allEvents;

    /**
     * @var integer
     *
     * @ORM\Column(name="ALL_STATUS", type="smallint", nullable=true)
     */
    private $allStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="AUTOSERV", type="string", length=30, nullable=true)
     */
    private $autoserv;

    /**
     * @var integer
     *
     * @ORM\Column(name="CURRUN", type="smallint", nullable=true)
     */
    private $currun;

    /**
     * @var string
     *
     * @ORM\Column(name="DO_OUTPUT", type="string", length=1, nullable=true)
     */
    private $doOutput;

    /**
     * @var integer
     *
     * @ORM\Column(name="FAILURE", type="smallint", nullable=true)
     */
    private $failure;

    /**
     * @var string
     *
     * @ORM\Column(name="JOB_FILTER", type="string", length=1, nullable=true)
     */
    private $jobFilter;

    /**
     * @var string
     *
     * @ORM\Column(name="JOB_NAME", type="string", length=64, nullable=true)
     */
    private $jobName;

    /**
     * @var string
     *
     * @ORM\Column(name="MON_MODE", type="string", length=1, nullable=true)
     */
    private $monMode;

    /**
     * @var string
     *
     * @ORM\Column(name="NAME", type="string", length=30, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="UJO_MONBRO_NAME_seq", allocationSize=1, initialValue=1)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="ON_HOLD", type="smallint", nullable=true)
     */
    private $onHold;

    /**
     * @var integer
     *
     * @ORM\Column(name="ON_ICE", type="smallint", nullable=true)
     */
    private $onIce;

    /**
     * @var integer
     *
     * @ORM\Column(name="RESTART", type="smallint", nullable=true)
     */
    private $restart;

    /**
     * @var integer
     *
     * @ORM\Column(name="RUNNING", type="smallint", nullable=true)
     */
    private $running;

    /**
     * @var integer
     *
     * @ORM\Column(name="SOUND", type="smallint", nullable=true)
     */
    private $sound;

    /**
     * @var integer
     *
     * @ORM\Column(name="STARTING", type="smallint", nullable=true)
     */
    private $starting;

    /**
     * @var integer
     *
     * @ORM\Column(name="SUCCESS", type="smallint", nullable=true)
     */
    private $success;

    /**
     * @var integer
     *
     * @ORM\Column(name="SUSPENDED", type="smallint", nullable=true)
     */
    private $suspended;

    /**
     * @var integer
     *
     * @ORM\Column(name="TERMINATE", type="smallint", nullable=true)
     */
    private $terminate;



    /**
     * Set afterTime
     *
     * @param string $afterTime
     * @return UjoMonbro
     */
    public function setAfterTime($afterTime)
    {
        $this->afterTime = $afterTime;

        return $this;
    }

    /**
     * Get afterTime
     *
     * @return string 
     */
    public function getAfterTime()
    {
        return $this->afterTime;
    }

    /**
     * Set alarm
     *
     * @param integer $alarm
     * @return UjoMonbro
     */
    public function setAlarm($alarm)
    {
        $this->alarm = $alarm;

        return $this;
    }

    /**
     * Get alarm
     *
     * @return integer 
     */
    public function getAlarm()
    {
        return $this->alarm;
    }

    /**
     * Set alarmVerif
     *
     * @param integer $alarmVerif
     * @return UjoMonbro
     */
    public function setAlarmVerif($alarmVerif)
    {
        $this->alarmVerif = $alarmVerif;

        return $this;
    }

    /**
     * Get alarmVerif
     *
     * @return integer 
     */
    public function getAlarmVerif()
    {
        return $this->alarmVerif;
    }

    /**
     * Set allEvents
     *
     * @param integer $allEvents
     * @return UjoMonbro
     */
    public function setAllEvents($allEvents)
    {
        $this->allEvents = $allEvents;

        return $this;
    }

    /**
     * Get allEvents
     *
     * @return integer 
     */
    public function getAllEvents()
    {
        return $this->allEvents;
    }

    /**
     * Set allStatus
     *
     * @param integer $allStatus
     * @return UjoMonbro
     */
    public function setAllStatus($allStatus)
    {
        $this->allStatus = $allStatus;

        return $this;
    }

    /**
     * Get allStatus
     *
     * @return integer 
     */
    public function getAllStatus()
    {
        return $this->allStatus;
    }

    /**
     * Set autoserv
     *
     * @param string $autoserv
     * @return UjoMonbro
     */
    public function setAutoserv($autoserv)
    {
        $this->autoserv = $autoserv;

        return $this;
    }

    /**
     * Get autoserv
     *
     * @return string 
     */
    public function getAutoserv()
    {
        return $this->autoserv;
    }

    /**
     * Set currun
     *
     * @param integer $currun
     * @return UjoMonbro
     */
    public function setCurrun($currun)
    {
        $this->currun = $currun;

        return $this;
    }

    /**
     * Get currun
     *
     * @return integer 
     */
    public function getCurrun()
    {
        return $this->currun;
    }

    /**
     * Set doOutput
     *
     * @param string $doOutput
     * @return UjoMonbro
     */
    public function setDoOutput($doOutput)
    {
        $this->doOutput = $doOutput;

        return $this;
    }

    /**
     * Get doOutput
     *
     * @return string 
     */
    public function getDoOutput()
    {
        return $this->doOutput;
    }

    /**
     * Set failure
     *
     * @param integer $failure
     * @return UjoMonbro
     */
    public function setFailure($failure)
    {
        $this->failure = $failure;

        return $this;
    }

    /**
     * Get failure
     *
     * @return integer 
     */
    public function getFailure()
    {
        return $this->failure;
    }

    /**
     * Set jobFilter
     *
     * @param string $jobFilter
     * @return UjoMonbro
     */
    public function setJobFilter($jobFilter)
    {
        $this->jobFilter = $jobFilter;

        return $this;
    }

    /**
     * Get jobFilter
     *
     * @return string 
     */
    public function getJobFilter()
    {
        return $this->jobFilter;
    }

    /**
     * Set jobName
     *
     * @param string $jobName
     * @return UjoMonbro
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
     * Set monMode
     *
     * @param string $monMode
     * @return UjoMonbro
     */
    public function setMonMode($monMode)
    {
        $this->monMode = $monMode;

        return $this;
    }

    /**
     * Get monMode
     *
     * @return string 
     */
    public function getMonMode()
    {
        return $this->monMode;
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
     * Set onHold
     *
     * @param integer $onHold
     * @return UjoMonbro
     */
    public function setOnHold($onHold)
    {
        $this->onHold = $onHold;

        return $this;
    }

    /**
     * Get onHold
     *
     * @return integer 
     */
    public function getOnHold()
    {
        return $this->onHold;
    }

    /**
     * Set onIce
     *
     * @param integer $onIce
     * @return UjoMonbro
     */
    public function setOnIce($onIce)
    {
        $this->onIce = $onIce;

        return $this;
    }

    /**
     * Get onIce
     *
     * @return integer 
     */
    public function getOnIce()
    {
        return $this->onIce;
    }

    /**
     * Set restart
     *
     * @param integer $restart
     * @return UjoMonbro
     */
    public function setRestart($restart)
    {
        $this->restart = $restart;

        return $this;
    }

    /**
     * Get restart
     *
     * @return integer 
     */
    public function getRestart()
    {
        return $this->restart;
    }

    /**
     * Set running
     *
     * @param integer $running
     * @return UjoMonbro
     */
    public function setRunning($running)
    {
        $this->running = $running;

        return $this;
    }

    /**
     * Get running
     *
     * @return integer 
     */
    public function getRunning()
    {
        return $this->running;
    }

    /**
     * Set sound
     *
     * @param integer $sound
     * @return UjoMonbro
     */
    public function setSound($sound)
    {
        $this->sound = $sound;

        return $this;
    }

    /**
     * Get sound
     *
     * @return integer 
     */
    public function getSound()
    {
        return $this->sound;
    }

    /**
     * Set starting
     *
     * @param integer $starting
     * @return UjoMonbro
     */
    public function setStarting($starting)
    {
        $this->starting = $starting;

        return $this;
    }

    /**
     * Get starting
     *
     * @return integer 
     */
    public function getStarting()
    {
        return $this->starting;
    }

    /**
     * Set success
     *
     * @param integer $success
     * @return UjoMonbro
     */
    public function setSuccess($success)
    {
        $this->success = $success;

        return $this;
    }

    /**
     * Get success
     *
     * @return integer 
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * Set suspended
     *
     * @param integer $suspended
     * @return UjoMonbro
     */
    public function setSuspended($suspended)
    {
        $this->suspended = $suspended;

        return $this;
    }

    /**
     * Get suspended
     *
     * @return integer 
     */
    public function getSuspended()
    {
        return $this->suspended;
    }

    /**
     * Set terminate
     *
     * @param integer $terminate
     * @return UjoMonbro
     */
    public function setTerminate($terminate)
    {
        $this->terminate = $terminate;

        return $this;
    }

    /**
     * Get terminate
     *
     * @return integer 
     */
    public function getTerminate()
    {
        return $this->terminate;
    }
}
