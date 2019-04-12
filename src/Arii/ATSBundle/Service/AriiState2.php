<?php
// src/Arii/JOCBundle/Service/AriiState.php
/*
 * Recupere les données et fournit un tableau pour les composants DHTMLx
 */ 
namespace Arii\ATSBundle\Service;

class AriiState2
{
    protected $portal;
    protected $autosys;    
    protected $tools;
    protected $TZ;

    public function __construct (
            \Arii\CoreBundle\Service\AriiPortal $portal,
            \Arii\CoreBundle\Service\AriiTools $tools,
            \Arii\ATSBundle\Service\AriiAutosys $autosys ) {

        date_default_timezone_set('UTC');
        $this->portal = $portal;
        $this->autosys = $autosys;
        $this->tools = $tools;
        $this->TZ = $portal->getTimeZone();
    }

/*********************************************************************
 * Informations de connexions
 *********************************************************************/
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
        
    public function Alarms($em) {
      
        // On regarde les chaines stoppés
        // On complete avec les ordres stockés
        $Alarms = $em->getRepository("AriiATSBundle:UjoAlarm")->findOpenIssues();
        $Result = [];
        $Nb = [];        
        $Colors = $this->portal->getColors();
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
                $Result[$id]['jobColor'] = (isset($Colors[$status]['bgcolor'])?$Colors[$status]['bgcolor']:'white'); 
                $Result[$id]['color'] = (isset($Colors[$alarm]['bgcolor'])?$Colors[$alarm]['bgcolor']:'white'); 
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

    public function AuditJobs($em) {
      
        // On regarde les chaines stoppés
        // On complete avec les ordres stockés
        $Audits = $em->getRepository("AriiATSBundle:UjoAuditInfo")->findJobs();
        $Result = [];
        foreach ($Audits as $Audit) {
            $id = $Audit['auditInfoNum'];
            // traitement du sendevent           
            $job = $Audit['value1'].$Audit['value2'];
            list($login,$machine) = explode('@',$Audit['entity']);
               
            // Decoupage 
            $Result[$id] =  [
                'id' => $id,
                'command' => 'sendevent',
                'event' => $Audit['attribute'],
                'job' => $job,
                'user' => $login,
                'host' => $machine,
                'is_edit' => $Audit['isEdit'],
                'date' => new \DateTime('@'.$Audit['time'])                                
            ];
            $Result[$id]['date']->setTimezone($this->TZ);
        }
        return $Result;
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

    public function Boxes($em) {
      
        $Boxes = $em->getRepository("AriiATSBundle:UjoJobst")->findBoxes();
        foreach ($Boxes as $k=>$Box) {
        }
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

}
