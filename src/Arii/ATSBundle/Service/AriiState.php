<?php
// src/Arii/JOCBundle/Service/AriiState.php
/*
 * Recupere les données et fournit un tableau pour les composants DHTMLx
 */ 
namespace Arii\ATSBundle\Service;

class Ariistate
{
    protected $autosys;    
    protected $tools;
    protected $TZ;
    protected $offset;

    public function __construct (
            \Arii\ATSBundle\Service\AriiAutosys $autosys,
            \Arii\CoreBundle\Service\AriiTools $tools ) {

        date_default_timezone_set('UTC');
        $this->autosys = $autosys;
        $this->tools = $tools;
        $this->TZ = $autosys->getTimeZone();
        $this->offset = timezone_offset_get( $this->TZ, new \DateTime() );
    }

    public function getRepo($instanceId) {
        return $this->autosys->getRepo($instanceId);
    }
/*********************************************************************
 * Simplification
 *********************************************************************/
    public function JobIdbyName($em,$jobId) {
      
        if (is_numeric($jobId))
            return $jobId;
        
        // On complete avec les ordres stockés
        $Job = $em->getRepository("AriiATSBundle:UjoJob")->findIdByName($jobId);
        if (isset($Job[0]['joid']))
            return $Job[0]['joid'];        
        return;
    }    

/*********************************************************************
 * Infos sur l'instance Autosys
 *********************************************************************/
    // La configuration Autosys du référentiel en cours
    public function Repo($em) {     
        $Infos = $em->getRepository("AriiATSBundle:UjoAlamode")->findAll();
        foreach($Infos as $Info) {
            $type = $Info->getType();
            $int[$type] = $Info->getIntVal();
            $str[$type] = $Info->getStrVal();            
        }
        return [
            "autoserv" => $str['AUTOSERV'],
            "autouser" => $str['AUTOUSER'],
            "version" => $str['VERSION'],
            "aedbVersion" => $str['AEDB_VERSION'],
            "serverTimezone" => $str['SERVER_TIMEZONE'],
            "remoteAthentication" => ($int['remauth']==1?true:false),
            "localhost" => $str['LOCALHOST'],
            "autotrack" => $int['AutoTrack'],
            "autoRemoteDir" => $str['AUTO_REMOTE_DIR'],
            "jobEditDate" => $str['job_edit_stamp'],
            "gmt_offset" => $str['gmt_offset'],
            "dualMode" => ($int['DB']==2?true:false),
            "job" => $int['JOB'],
            "evt" => $int['EVT'],
            "sec" => $int['SEC'],
            "appsrvQueueId" => $int['appsrv_queue_id']
            
        ];
    }
    
/*********************************************************************
 * Informations de connexions
 *********************************************************************/
    public function Machines($em,$Filter) {      
        $Machines = $em->getRepository("AriiATSBundle:UjoMachine")->findMachines($Filter);
        foreach ($Machines as $k=>$Machine) {
            $Machines[$k]['operatingSystem'] = $this->autosys->OpSys($Machine['operatingSystem']);
            $Machines[$k]['machineStatus'] = $this->autosys->MachineStatus($Machine['machineStatus']);
        }
        return $Machines;
    }

    public function Agents($em,$parentName=null) {
      
        // On regarde les chaines stoppés
        // On complete avec les ordres stockés
        $Agents = $em->getRepository("AriiATSBundle:UjoMachine")->findAgents($parentName);
        
        foreach ($Agents as $k=>$Agent) {
            switch ($Agent['machStatus']) {
                case 'o':
                    $Agents[$k]['Status'] = 'Online';
                    break;
                case 'm':
                    $Agents[$k]['Status'] = 'Offline';
                    break;
                default:
                    $Agents[$k]['Status'] = $Agent['machStatus'];
                    break;
            }
            switch ($Agent['opsys']) {
                case 3:
                    $Agents[$k]['opSys'] = 'Unix';
                    break;
                case 5:
                    $Agents[$k]['opSys'] = 'iSeries';
                    break;
                case 8:
                    $Agents[$k]['opSys'] = 'Windows';
                    break;
                default:
                    $Agents[$k]['opSys'] = $Agent['opsys'];
                    break;
            }
        }
        return $Agents;        
    }    

    public function Alarms($em,$Filter) {
      
        // On regarde les chaines stoppés
        // On complete avec les ordres stockés
        $Alarms = $em->getRepository("AriiATSBundle:UjoAlarm")->findAlarms($Filter);
        foreach ($Alarms as $k=>$Alarm) {
            $alarm   = $this->autosys->Alarm($Alarm['alarm']);
            $jobname = $Alarm['jobName'];
            $Alarms[$k]['alarm'] = $alarm;
            $Alarms[$k]['alarmTime'] = new \DateTime('@'.$Alarm['alarmTime']);
            $Alarms[$k]['alarmTime']->setTimezone($this->TZ);
            $Alarms[$k]['status'] = $this->autosys->Status($Alarm['status']);
            $Alarms[$k]['state'] =  $this->autosys->AlarmState($Alarm['state']);
        }
        return $Alarms;
    }
    
    // Utile ?
    // Alarmes consolidees
    public function Alarms2($em,$Filter) {
      
        // On regarde les chaines stoppés
        // On complete avec les ordres stockés
        $Alarms = $em->getRepository("AriiATSBundle:UjoAlarm")->findAlarms($Filter);
        $Result = [];
        $Nb = [];        
        foreach ($Alarms as $Alarm) {
            $alarm   = $this->autosys->Alarm($Alarm['alarm']);
            $jobname = $Alarm['jobName'];
            $id = "$alarm#$jobname";
            if (isset($Nb[$id])) {
                $Nb[$id]++;
                $Result[$id]['firstTime'] = new \DateTime('@'.$Alarm['alarmTime']);
                $Result[$id]['firstTime']->setTimezone($this->TZ);
            }
            else {
                $Result[$id] = $Alarm;
                $Nb[$id]=1;
                $Result[$id]['alarm'] = $alarm;
                $Result[$id]['alarmTime'] = new \DateTime('@'.$Alarm['alarmTime']);
                $Result[$id]['alarmTime']->setTimezone($this->TZ);
                $Result[$id]['stateTime'] = new \DateTime('@'.$Alarm['stateTime']);
                $Result[$id]['stateTime']->setTimezone($this->TZ);
                $status = $this->autosys->Status($Alarm['status']);
                $Result[$id]['status'] = $status;
                $state =  $this->autosys->AlarmState($Alarm['state']);
                $Result[$id]['state'] = $state;
                $Result[$id]['stateGrid'] = ($state=='ACKNOWLEDGED'?'ACK.':$state);
                
                // Couleurs paticuliere
                if (
                        ($alarm=='JOBFAILURE') && ($status!='FAILURE')
                    || ($alarm=='MAXRUNALARM') && ($status!='RUNNING')
                    || ($alarm=='MINRUNALARM') && ($status=='RUNNING')
                    ){                    
                    $Result[$id]['color'] = 'white';                    
                }
            }
            $Result[$id]['nb'] = $Nb[$id];            
        }
        
        // Format lineaire
        $Alarms = [];
        foreach ($Result as $r) {
            array_push($Alarms, $r);
        }
        return $Alarms;
    }

    public function Jobs($em,$Filter) {
      
        if (isset($Filter['status']))
            $Filter['status'] = $this->autosys->StatusToCodes($Filter['status']);
        if (isset($Filter['jobType']))
            $Filter['jobType'] = $this->autosys->jobTypeToCode($Filter['jobType']);
        
        $Jobs = $em->getRepository("AriiATSBundle:UjoJobst")->findJobs($Filter);
        foreach ($Jobs as $k=>$Job) {
            $Jobs[$k]['status'] = $this->autosys->Status($Job['status']);
            foreach(array('statusTime','lastStart','lastEnd','nextStart') as $time) {
                $Jobs[$k][$time] = new \DateTime('@'.$Job[$time]);
                $Jobs[$k][$time]->setTimezone($this->TZ);
            }
            $Jobs[$k]['jobType'] = $this->autosys->CodeToJobType($Job['jobType']);
        }
        return $Jobs;
    }

    public function JobRuns($em,$jobId,$Filter) { 
        $jobId = $this->JobIdbyName($em,$jobId);
        $JobRuns = $em->getRepository("AriiATSBundle:UjoJobRuns")->findJobRuns($jobId,$Filter);
        foreach ($JobRuns as $k=>$JobRun) {
            $JobRuns[$k]['status'] = $this->autosys->Status($JobRun['status']);
            list($JobRuns[$k]['color'])  = $this->autosys->ColorStatus($JobRuns[$k]['status']);
            if ($JobRuns[$k]['status']=='SUCCESS') 
                $JobRuns[$k]['success']=1;
            else 
                $JobRuns[$k]['success']=0;
            
            $JobRuns[$k]['startTime'] = new \DateTime('@'.$JobRun['startime']);
            $JobRuns[$k]['startTime']->setTimezone($this->TZ);
            $JobRuns[$k]['endTime'] = new \DateTime('@'.$JobRun['endtime']);
            $JobRuns[$k]['endTime']->setTimezone($this->TZ);
            
            $JobRuns[$k]['starttime'] = $JobRun['startime'] + $this->offset;
            $JobRuns[$k]['endtime']   = $JobRun['endtime']  + $this->offset;
            
            // peut etre interessant de conserver une heure GMT quelque part 
        }
        return $JobRuns;
    }
    
    // Les jobs d'une boite
    public function BoxStatus($em,$jobId,$Filter) {     
        $jobId = $this->JobIdbyName($em,$jobId);
        $JobRuns = $em->getRepository("AriiATSBundle:UjoJobSt")->findBoxStatus($jobId,$Filter);
        foreach ($JobRuns as $k=>$JobRun) {
        }
        return $JobRuns;
    }
    
    public function AuditSendevent($em) {
      
        // On regarde les chaines stoppés
        // On complete avec les ordres stockés
        $Audits = $em->getRepository("AriiATSBundle:UjoAuditInfo")->findSendevent();
        $Result = [];
        foreach ($Audits as $Audit) {
            $id = $Audit['auditInfoNum'];
            // traitement du sendevent           
            $command = $Audit['value1'].$Audit['value2'];
            $I = explode('" "',substr($command,strpos($command,'"')+1));    
            $i = 0;
            $Infos = [ '-E' => '', '-J' => '', '-s' => '', '-G' => '', '-C' => '', '-S' => '' ]; # RAZ
            while (isset($I[$i])) {
                $var = $I[$i];
                $val = str_replace('"','',$I[$i+1]);
                $Infos[$var] = $val;
                $i+=2;
            }
            list($login,$machine) = explode('@',$Audit['entity']);
            // Decoupage 
            $Result[$id] =  [
                'command' => 'sendevent',
                'event' => $Infos['-E'],
                'job' => $Infos['-J'],
                'status' => $Infos['-s'],
                'global' => $Infos['-G'],
                'instance' => $Infos['-S'],
                'user' => $login,
                'domain' => $machine,
                'comment' => $Infos['-C'],
                'date' => new \DateTime('@'.$Audit['time'])                                
            ];
            $Result[$id]['date']->setTimezone($this->TZ);
        }
        return $Result;
    }

    public function Audit($em,$Filter) {

        $Infos = $em->getRepository("AriiATSBundle:UjoAuditInfo")->findAudit($Filter);
        foreach ($Infos as $k=>$Info) {
            $Infos[$k]['eventTime'] = new \DateTime('@'.$Info['eventTime']);
            $Infos[$k]['eventTime']->setTimezone($this->TZ);
            $Infos[$k]['eventType'] = $this->autosys->eventType($Info['eventType']);
            if ($Info['value2']!='') {
                $Infos[$k]['value'] .= (substr($Infos[$k]['value2'],0,1)=='"'?substr($Infos[$k]['value2'],1,strlen($Infos[$k]['value2'])-2):$Infos[$k]['value2']); 
                $Infos[$k]['value2'] = null;                
            }
        }
        return $Infos;
    }

    public function AuditMsg($em,$auditId,$Filter) {
      
        $Infos = $em->getRepository("AriiATSBundle:UjoAuditMsg")->findAudit($auditId,$Filter);
        foreach ($Infos as $k=>$Info) {
            $Infos[$k]['isEdit'] = ($Info['isEdit']=='N'?FALSE:TRUE);
            $Infos[$k]['value'] = (substr($Infos[$k]['value'],0,1)=='"'?substr($Infos[$k]['value'],1,strlen($Infos[$k]['value'])-2):$Infos[$k]['value']);
            if ($Info['value2']!='') {
                $Infos[$k]['value'] .= (substr($Infos[$k]['value2'],0,1)=='"'?substr($Infos[$k]['value2'],1,strlen($Infos[$k]['value2'])-2):$Infos[$k]['value2']); 
                $Infos[$k]['value2'] = null;                
            }
        }
        return $Infos;
    }
    
    public function AuditJobs($em) {
      
        // On regarde les chaines stoppés
        // On complete avec les ordres stockés
        $Jobs = $em->getRepository("AriiATSBundle:UjoAuditInfo")->findJobs();
        $Result = [];
        foreach ($Jobs as $k=>$Job) {
            $Jobs[$k]['isEdit'] = ($Job['isEdit']=='Y'?true:false);
        }
        return $Jobs;
    }

    public function AuditJob($em,$id) {
      
        // On regarde les chaines stoppés
        // On complete avec les ordres stockés
        
        $Audits = $em->getRepository("AriiATSBundle:UjoAuditMsg")->findJob($id);

        $Result = [];
        foreach ($Audits as $Audit) {
            array_push($Result, [
                'seqNo' => $Audit['seqNo'],
                'attribute' => $Audit['attribute'],
                'value' => $Audit['value1'].$Audit['value2'],
                'is_edit' => ($Audit['isEdit']=='Y'?1:0),
                'color' => ($Audit['isEdit']=='Y'?"#000":"#999")
            ] );
        }
        return $Result;
    }

    public function Boxes($em, $Filter) {
        $Boxes = $em->getRepository("AriiATSBundle:UjoJobst")->findBoxes($Filter);
        return $Boxes;
    }

    public function Processes($em) {
      
        $Boxes = $em->getRepository("AriiATSBundle:UjoJobst")->findProcesses();
        foreach ($Boxes as $k=>$Box) {
            $Boxes[$k]['status'] = $this->autosys->Status($Box['status']);
            $Boxes[$k]['lastStart'] = new \DateTime('@'.$Box['lastStart']);
            $Boxes[$k]['lastEnd'] = new \DateTime('@'.$Box['lastEnd']);
            $Boxes[$k]['nextStart'] = new \DateTime('@'.$Box['nextStart']);
            $Boxes[$k]['statusTime'] = new \DateTime('@'.$Box['statusTime']);
        }
        return $Boxes;
    }
    
    public function Queues($em) {
      
        // On regarde les chaines stoppés
        // On complete avec les ordres stockés
        $Machines = $em->getRepository("AriiATSBundle:UjoMachine")->findQueues();
        $Queues = [];
        foreach ($Machines as $k=>$Machine) {
            $q = $Machine['parentName'];
            if ($q==' ') continue;
            
            // Load
            if (!isset($Queues[$q])) {
                $Queues[$q]['name']=$q;
                $Queues[$q]['maxLoad']=$Queues[$q]['status']=$Queues[$q]['machines']=$Queues[$q]['online']=$Queues[$q]['offline']=0;
            }
            if ($Machine['machStatus']=='o')
                $Queues[$q]['maxLoad'] += $Machine['maxLoad'];            
            if ($Machine['machStatus']=='o')
                $Queues[$q]['online']++;
            else 
                $Queues[$q]['offline']++;
            $Queues[$q]['machines']++;            
        }
        // Status global
        foreach ($Queues as $q=>$Queue) {
            if ($Queue['machines']==$Queue['online']) 
                $Queues[$q]['status'] = 'Online';
            elseif ($Queue['machines']==$Queue['offline']) 
                $Queues[$q]['status'] = 'Offline';
            else 
                $Queues[$q]['status'] = 'Incomplete';
            $Queues[$q]['percent'] = round($Queue['online']*100/$Queue['machines']);
            if ($Queues[$q]['maxLoad'] == 0)
                $Queues[$q]['maxLoad'] = ''; 
            $Queues[$q]['id'] = $Queue['name'];
        }
        return array_values($Queues);
    }    

    // Infos pour une seule queue
    public function Queue($em,$queueName) {
        // On regarde les chaines stoppés
        // On complete avec les ordres stockés
        $Agents = $em->getRepository("AriiATSBundle:UjoMachine")->findQueue($queueName);
        foreach ($Agents as $k=>$Agent) {
            switch ($Agent['machStatus']) {
                case 'o':
                    $Agents[$k]['Status'] = 'Online';
                    break;
                case 'm':
                    $Agents[$k]['Status'] = 'Offline';
                    break;
                default:
                    $Agents[$k]['Status'] = $Agent['machStatus'];
                    break;
            }
            switch ($Agent['opsys']) {
                case 3:
                    $Agents[$k]['opSys'] = 'Unix';
                    break;
                case 5:
                    $Agents[$k]['opSys'] = 'iSeries';
                    break;
                case 8:
                    $Agents[$k]['opSys'] = 'Windows';
                    break;
                default:
                    $Agents[$k]['opSys'] = $Agent['opsys'];
                    break;
            }
        }
        return $Agents;
    }
   
    
    public function WaitQueues($em) {
      
        // On regarde les chaines stoppés
        // On complete avec les ordres stockés
        $Queues = $em->getRepository("AriiATSBundle:UjoWaitQue")->findWaitQueues();
        
        foreach ($Queues as $k=>$Queue) {
            switch ($Queue['machStatus']) {
                case 'o':
                    $Queues[$k]['Status'] = 'Online';
                    break;
                case 'm':
                    $Queues[$k]['Status'] = 'Offline';
                    break;
                default:
                    $Queues[$k]['Status'] = $Queue['machStatus'];
                    break;
            }
            switch ($Queue['opsys']) {
                case 3:
                    $Queues[$k]['opSys'] = 'Unix';
                    break;
                case 5:
                    $Queues[$k]['opSys'] = 'iSeries';
                    break;
                case 8:
                    $Queues[$k]['opSys'] = 'Windows';
                    break;
                default:
                    $Queues[$k]['opSys'] = $Queue['opsys'];
                    break;
            }
        }
        return $Queues;        
    }    

    public function Status($em,$Filter) {
        // Conversion du status em liste d'entier
        if (isset($Filter['status']))
            $Filter['status'] = $this->autosys->StatusToCodes($Filter['status']);

        $Status = $em->getRepository("AriiATSBundle:UjoJobst")->findStatus($Filter);
        foreach ($Status as $k=>$Jobst) {
            $Status[$k]['statusName'] = $this->autosys->Status($Jobst['statusId']);
            foreach (['statusTime','lastStart','lastEnd','nextStart'] as $time) {
                $Status[$k][$time] = new \DateTime('@'.$Jobst[$time]);
                $Status[$k][$time]->setTimezone($this->TZ);
            }
            $Status[$k]['jobType'] =  $this->autosys->CodeToJobType($Jobst['jobType']);
            $Status[$k]['runMachine'] = ($Status[$k]['runMachine']==" "?null:$Status[$k]['runMachine']);
            $Status[$k]['pid'] = ($Status[$k]['pid']==-1?null:$Status[$k]['pid']);
        }
        return $Status;
    }
    
    public function Schedulers($em) {
      
        // On regarde les chaines stoppés
        // On complete avec les ordres stockés
        $Schedulers = $em->getRepository("AriiATSBundle:UjoHaProcess")->findSchedulers();
        $Role=[ 
            '1' => 'Primary',
            '2' => 'Shadow',
            '3' => 'Tie Breaker'
        ];
        foreach ($Schedulers as $k=>$Scheduler) {
            $role = $Scheduler['haDesignatorId'];
            $Schedulers[$k]['id'] = $role;
            $Schedulers[$k]['role'] = $Role[$role];
            $local = new \DateTimeZone('GMT');
            $dt = new \DateTime("@".$Scheduler['timeStamp'],$local);
            $dt->setTimezone($this->TZ);
            $Schedulers[$k]['statusTime'] = $dt->format('Y-m-d H:i:s');
            
            // on en profite pour voir si c'est dans les temps
            $now = new \DateTime();
            $Schedulers[$k]['delay'] = $now->getTimestamp() - $dt->getTimestamp();
            if ($Schedulers[$k]['delay']>120)
                $Schedulers[$k]['status'] = 'DEAD';
            elseif ($Schedulers[$k]['delay']<30)
                $Schedulers[$k]['status'] = 'ALIVE';
            else
                $Schedulers[$k]['status'] = 'DELAYED';   
        }
        return $Schedulers;        
    }
    
    public function AppServers($em) {
      
        // On regarde les chaines stoppés
        // On complete avec les ordres stockés
        $AppServers = $em->getRepository("AriiATSBundle:UjoMaProcess")->findAppServers();
        foreach ($AppServers as $k=>$AppServer) {
            $local = new \DateTimeZone('GMT');
            $dt = new \DateTime("@".$AppServer['timeStamp'],$local);
            $dt->setTimezone($this->TZ);
            $AppServers[$k]['statusTime'] = $dt->format('Y-m-d H:i:s');
        }
        return $AppServers;        
    }    

    public function Users($em,$Filter) {      
        $Users = $em->getRepository("AriiATSBundle:UjoCred")->findUsers($Filter);
        return $Users;
    }

    public function Calendars($em,$Filter) {      
        $Calendars = $em->getRepository("AriiATSBundle:UjoCalendar")->findCalendars($Filter);
        $Date = new \DateTime();
        $now = $Date->getTimestamp();
        foreach ($Calendars as $k=>$Calendar) {
            $End = new \DateTime($Calendar['maxDate']);
            $Calendars[$k]['isActive'] = ($now-$End->getTimestamp()<0);
            if (isset($Filter['isActive']) and ($Filter['isActive']!=$Calendars[$k]['isActive']))
                unset($Calendars[$k]);
        }
        return $Calendars;
    }

    public function ExtendedCalendars($em,$Filter) {      
        $Calendars = $em->getRepository("AriiATSBundle:UjoExtCalendar")->findCalendars($Filter);
        return $Calendars;
    }

    public function Days($em,$Filter) {      
        $Calendars = $em->getRepository("AriiATSBundle:UjoCalendar")->findDays($Filter);
        return $Calendars;
    }
    
}
