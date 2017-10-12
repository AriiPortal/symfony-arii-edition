<?php

namespace Arii\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cron
 *
 * @ORM\Table(name="ARII_JOB")
 * @ORM\Entity()
 */
class Job
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
     * @var string
     *
     * @ORM\Column(name="requester", type="string", length=64, nullable=true)
     */        
    private $requester;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=true)
     */        
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="app_name", type="string", length=64, nullable=true)
     */        
    private $app_name;

    /**
     * @var string
     *
     * @ORM\Column(name="group_name", type="string", length=64, nullable=true)
     */        
    private $group_name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=64, nullable=true)
     */        
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="command_type", type="string", length=64, nullable=true)
     */        
    private $command_type;
    
    /**
     * @var string
     *
     * @ORM\Column(name="db_update", type="boolean", nullable=true)
     */        
    private $db_update;

    /**
     * @var string
     *
     * @ORM\Column(name="command", type="string", length=64, nullable=true)
     */        
    private $command;

    /**
     * @var string
     *
     * @ORM\Column(name="owner", type="string", length=64, nullable=true)
     */        
    private $owner;

    /**
     * @var string
     *
     * @ORM\Column(name="machine", type="string", length=64, nullable=true)
     */        
    private $machine;

    /**
     * @var string
     *
     * @ORM\Column(name="trigger_date_time", type="boolean", nullable=true)
     */        
    private $trigger_date_time;

    /**
     * @var string
     *
     * @ORM\Column(name="date", type="string", length=64, nullable=true)
     */        
    private $date;
    
    /**
     * @var string
     *
     * @ORM\Column(name="days_of_week", type="string", length=64, nullable=true)
     */        
    private $days_of_week;
    
    /**
     * @var string
     *
     * @ORM\Column(name="run_calendar", type="string", length=64, nullable=true)
     */        
    private $run_calendar;
    
    /**
     * @var string
     *
     * @ORM\Column(name="exclude_calendar", type="string", length=64, nullable=true)
     */        
    private $exclude_calendar;
    
    /**
     * @var string
     *
     * @ORM\Column(name="trigger_file_watcher", type="boolean", nullable=true)
     */        
    private $trigger_file_watcher;

    /**
     * @var string
     *
     * @ORM\Column(name="file_watcher", type="string", length=255, nullable=true)
     */        
    private $file_watcher;
    
    /**
     * @var string
     *
     * @ORM\Column(name="success_codes", type="string", length=255, nullable=true)
     */        
    private $success_codes;

    /**
     * @var string
     *
     * @ORM\Column(name="start_times", type="string", length=64, nullable=true)
     */        
    private $start_times;

    /**
     * @var string
     *
     * @ORM\Column(name="dependencies", type="string", length=64, nullable=true)
     */        
    private $dependencies;
    
    /**
     * @var string
     *
     * @ORM\Column(name="successors", type="string", length=64, nullable=true)
     */        
    private $successors;
    
    /**
     * @var string
     *
     * @ORM\Column(name="not_running", type="string", length=64, nullable=true)
     */        
    private $not_running;

    /**
     * @var string
     *
     * @ORM\Column(name="resources", type="string", length=64, nullable=true)
     */        
    private $resources;    
    
    /**
     * @var string
     *
     * @ORM\Column(name="resources_value", type="integer", nullable=true)
     */        
    private $resources_value;    
    
    /**
     * @var string
     *
     * @ORM\Column(name="instructions", type="string", length=1024, nullable=true)
     */        
    private $instructions;    
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="restart", type="boolean", nullable=true)
     */        
    private $restart;

    /**
     * @var integer
     *
     * @ORM\Column(name="criticity", type="integer", nullable=true)
     */        
    private $criticity;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_run_time", type="integer", nullable=true)
     */        
    private $max_run_time;
    
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
     * Set name
     *
     * @param string $name
     * @return Requests
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
     * Set app_name
     *
     * @param string $appName
     * @return Requests
     */
    public function setAppName($appName)
    {
        $this->app_name = $appName;

        return $this;
    }

    /**
     * Get app_name
     *
     * @return string 
     */
    public function getAppName()
    {
        return $this->app_name;
    }

    /**
     * Set group_name
     *
     * @param string $groupName
     * @return Requests
     */
    public function setGroupName($groupName)
    {
        $this->group_name = $groupName;

        return $this;
    }

    /**
     * Get group_name
     *
     * @return string 
     */
    public function getGroupName()
    {
        return $this->group_name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Requests
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set command
     *
     * @param string $command
     * @return Requests
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
     * Set owner
     *
     * @param string $owner
     * @return Requests
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return string 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set machine
     *
     * @param string $machine
     * @return Requests
     */
    public function setMachine($machine)
    {
        $this->machine = $machine;

        return $this;
    }

    /**
     * Get machine
     *
     * @return string 
     */
    public function getMachine()
    {
        return $this->machine;
    }

    /**
     * Set triggers
     *
     * @param string $triggers
     * @return Requests
     */
    public function setTriggers($triggers)
    {
        $this->triggers = $triggers;

        return $this;
    }

    /**
     * Get triggers
     *
     * @return string 
     */
    public function getTriggers()
    {
        return $this->triggers;
    }

    /**
     * Set date
     *
     * @param string $date
     * @return Requests
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return string 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set days_of_week
     *
     * @param string $daysOfWeek
     * @return Requests
     */
    public function setDaysOfWeek($daysOfWeek)
    {
        $this->days_of_week = $daysOfWeek;

        return $this;
    }

    /**
     * Get days_of_week
     *
     * @return string 
     */
    public function getDaysOfWeek()
    {
        return $this->days_of_week;
    }

    /**
     * Set calendar
     *
     * @param string $calendar
     * @return Requests
     */
    public function setCalendar($calendar)
    {
        $this->calendar = $calendar;

        return $this;
    }

    /**
     * Get calendar
     *
     * @return string 
     */
    public function getCalendar()
    {
        return $this->calendar;
    }

    /**
     * Set start_times
     *
     * @param string $startTimes
     * @return Requests
     */
    public function setStartTimes($startTimes)
    {
        $this->start_times = $startTimes;

        return $this;
    }

    /**
     * Get start_times
     *
     * @return string 
     */
    public function getStartTimes()
    {
        return $this->start_times;
    }

    /**
     * Set dependencies
     *
     * @param string $dependencies
     * @return Requests
     */
    public function setDependencies($dependencies)
    {
        $this->dependencies = $dependencies;

        return $this;
    }

    /**
     * Get dependencies
     *
     * @return string 
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * Set not_running
     *
     * @param string $notRunning
     * @return Requests
     */
    public function setNotRunning($notRunning)
    {
        $this->not_running = $notRunning;

        return $this;
    }

    /**
     * Get not_running
     *
     * @return string 
     */
    public function getNotRunning()
    {
        return $this->not_running;
    }

    /**
     * Set resources
     *
     * @param string $resources
     * @return Requests
     */
    public function setResources($resources)
    {
        $this->resources = $resources;

        return $this;
    }

    /**
     * Get resources
     *
     * @return string 
     */
    public function getResources()
    {
        return $this->resources;
    }

    /**
     * Set resources_value
     *
     * @param integer $resourcesValue
     * @return Requests
     */
    public function setResourcesValue($resourcesValue)
    {
        $this->resources_value = $resourcesValue;

        return $this;
    }

    /**
     * Get resources_value
     *
     * @return integer 
     */
    public function getResourcesValue()
    {
        return $this->resources_value;
    }

    /**
     * Set requester
     *
     * @param string $requester
     * @return Requests
     */
    public function setRequester($requester)
    {
        $this->requester = $requester;

        return $this;
    }

    /**
     * Get requester
     *
     * @return string 
     */
    public function getRequester()
    {
        return $this->requester;
    }

    /**
     * Set command_type
     *
     * @param string $commandType
     * @return Requests
     */
    public function setCommandType($commandType)
    {
        $this->command_type = $commandType;

        return $this;
    }

    /**
     * Get command_type
     *
     * @return string 
     */
    public function getCommandType()
    {
        return $this->command_type;
    }

    /**
     * Set db_update
     *
     * @param boolean $dbUpdate
     * @return Requests
     */
    public function setDbUpdate($dbUpdate)
    {
        $this->db_update = $dbUpdate;

        return $this;
    }

    /**
     * Get db_update
     *
     * @return boolean 
     */
    public function getDbUpdate()
    {
        return $this->db_update;
    }

    /**
     * Set file_watcher
     *
     * @param string $fileWatcher
     * @return Requests
     */
    public function setFileWatcher($fileWatcher)
    {
        $this->file_watcher = $fileWatcher;

        return $this;
    }

    /**
     * Get file_watcher
     *
     * @return string 
     */
    public function getFileWatcher()
    {
        return $this->file_watcher;
    }

    /**
     * Set success_codes
     *
     * @param string $successCodes
     * @return Requests
     */
    public function setSuccessCodes($successCodes)
    {
        $this->success_codes = $successCodes;

        return $this;
    }

    /**
     * Get success_codes
     *
     * @return string 
     */
    public function getSuccessCodes()
    {
        return $this->success_codes;
    }

    /**
     * Set successors
     *
     * @param string $successors
     * @return Requests
     */
    public function setSuccessors($successors)
    {
        $this->successors = $successors;

        return $this;
    }

    /**
     * Get successors
     *
     * @return string 
     */
    public function getSuccessors()
    {
        return $this->successors;
    }

    /**
     * Set instructions
     *
     * @param string $instructions
     * @return Requests
     */
    public function setInstructions($instructions)
    {
        $this->instructions = $instructions;

        return $this;
    }

    /**
     * Get instructions
     *
     * @return string 
     */
    public function getInstructions()
    {
        return $this->instructions;
    }

    /**
     * Set restart
     *
     * @param boolean $restart
     * @return Requests
     */
    public function setRestart($restart)
    {
        $this->restart = $restart;

        return $this;
    }

    /**
     * Get restart
     *
     * @return boolean 
     */
    public function getRestart()
    {
        return $this->restart;
    }

    /**
     * Set run_calendar
     *
     * @param string $runCalendar
     * @return Requests
     */
    public function setRunCalendar($runCalendar)
    {
        $this->run_calendar = $runCalendar;

        return $this;
    }

    /**
     * Get run_calendar
     *
     * @return string 
     */
    public function getRunCalendar()
    {
        return $this->run_calendar;
    }

    /**
     * Set exclude_calendar
     *
     * @param string $excludeCalendar
     * @return Requests
     */
    public function setExcludeCalendar($excludeCalendar)
    {
        $this->exclude_calendar = $excludeCalendar;

        return $this;
    }

    /**
     * Get exclude_calendar
     *
     * @return string 
     */
    public function getExcludeCalendar()
    {
        return $this->exclude_calendar;
    }

    /**
     * Set trigger_date_time
     *
     * @param boolean $triggerDateTime
     * @return Requests
     */
    public function setTriggerDateTime($triggerDateTime)
    {
        $this->trigger_date_time = $triggerDateTime;

        return $this;
    }

    /**
     * Get trigger_date_time
     *
     * @return boolean 
     */
    public function getTriggerDateTime()
    {
        return $this->trigger_date_time;
    }

    /**
     * Set trigger_file_watcher
     *
     * @param boolean $triggerFileWatcher
     * @return Requests
     */
    public function setTriggerFileWatcher($triggerFileWatcher)
    {
        $this->trigger_file_watcher = $triggerFileWatcher;

        return $this;
    }

    /**
     * Get trigger_file_watcher
     *
     * @return boolean 
     */
    public function getTriggerFileWatcher()
    {
        return $this->trigger_file_watcher;
    }

    /**
     * Set criticity
     *
     * @param integer $criticity
     * @return Requests
     */
    public function setCriticity($criticity)
    {
        $this->criticity = $criticity;

        return $this;
    }

    /**
     * Get criticity
     *
     * @return integer 
     */
    public function getCriticity()
    {
        return $this->criticity;
    }

    /**
     * Set max_run_time
     *
     * @param integer $maxRunTime
     * @return Requests
     */
    public function setMaxRunTime($maxRunTime)
    {
        $this->max_run_time = $maxRunTime;

        return $this;
    }

    /**
     * Get max_run_time
     *
     * @return integer 
     */
    public function getMaxRunTime()
    {
        return $this->max_run_time;
    }
}
