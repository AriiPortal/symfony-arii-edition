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
                $Result[$id]['color'] = (isset($Colors[$state]['bgcolor'])?$Colors[$state]['bgcolor']:'white'); 
                $Result[$id]['state_grid'] = ($state=='ACKNOWLEDGED'?'ACK.':$state);
                
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
        return $Result;
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
            $id = $Audit['seqNo'];
            $Result[$id] =  [
                'attribute' => $Audit['attribute'],
                'value' => $Audit['value1'].$Audit['value2'],
                'is_edit' => ($Audit['isEdit']=='Y'?1:0),
                'color' => ($Audit['isEdit']=='Y'?"#000":"#999")
            ];
        }
        return $Result;
    }
    
}
