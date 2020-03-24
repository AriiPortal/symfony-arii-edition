<?php
namespace Arii\ATSBundle\Service;

class AriiJobDB
{
    protected $autosys;    
    protected $tools;
    protected $TZ;
    protected $offset;

    public function __construct (
            \Arii\ATSBundle\Service\AriiAutosys $autosys,
            \Arii\CoreBundle\Service\AriiTools $tools
             ) {

        date_default_timezone_set('UTC');
        $this->tools = $tools;
        $this->autosys = $autosys;
        $this->TZ = $autosys->getTimeZone();
        $this->offset = timezone_offset_get( $this->TZ, new \DateTime() );
    }

    public function getRepo($instanceId) {
        return $this->autosys->getRepo($instanceId);
    }
    
/*********************************************************************
 * Simplification
 *********************************************************************/
    public function getJobIdByName($em,$jobName) {      
        if (is_numeric($jobName))
            return $jobName;
        
        // On complete avec les ordres stockés
        $Job = $em->getRepository("AriiATSBundle:UjoJob")->findIdByName($jobName);
        return $Job['joid'];
    }    

    // Statut du job
    public function Children($em,$jobName,$Options,$depth=0) {
        if ($depth>$Options['depth'])
            return;
        $depth++;
        $jobId = $this->getJobIdByName($em,$jobName);
        $Children = $em->getRepository("AriiATSBundle:UjoJobst")->findChildren($jobId);
        $Result=[];
        foreach ($Children as $k=>$Child) {
            $id = $Child['jobId'];
            $name = $Child['jobName'];
            $Info = $Child;            
            $Info['jobType'] = $this->autosys->CodeToJobType($Child['jobType']);
            $Info['status'] = $this->autosys->Status($Child['status']);
            
            // Dependances ?
            if (isset($Options['dependencies']) and ($Options['dependencies']>0)) {
                $Conditions = $em->getRepository("AriiATSBundle:UjoJobCond")->findConditions($id,$Options);
                if (!empty($Conditions)) 
                    $Info['dependencies'] = $Conditions;
            }

            // Enfants
            $hasChild = $this->Children($em,$id,$Options,$depth);
            if (!empty($hasChild)) {
                $Info['children'] = $hasChild;
            }
            array_push($Result, $Info );            
        }
        return $Result;
    }

    // Statut du job
    public function Status($em,$jobName,$Filter) {   
        $jobId = $this->getJobIdByName($em,$jobName);
        $Status = $em->getRepository("AriiATSBundle:UjoJobSt")->findStatusByJobId($jobId,$Filter);
        $Status['timeOk'] = null;
        $Status['status'] = $this->autosys->Status($Status['status']);
        // Boolean
        foreach (['boxTerminator','jobTerminator','dateConditions','hasCondition','hasNotification'] as $f) {
            $Status[$f] = ($Status[$f]==1?true:false);
        }
        // Undefined
        foreach (['autoDelete','runPriority'] as $f) {
            $Status[$f] = ($Status[$f]==-1?null:true);
        }
        foreach (['lastStart','lastEnd','statusTime','nextStart'] as $f) {
            if ($Status[$f]==0) {
                $Status[$f] = null;                
            }
            else {
                $Status[$f] = new \DateTime('@'.$Status[$f]);
                $Status[$f]->setTimezone($this->TZ);
            }
        }
        return $Status;
    }
        
    public function Runs($em,$jobName,$Filter) { 
        $jobId = $this->getJobIdByName($em,$jobName);
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
            
            #$JobRuns[$k]['starttime'] = $JobRun['startime'] + $this->offset;
            #$JobRuns[$k]['endtime']   = $JobRun['endtime']  + $this->offset;

            $JobRuns[$k]['startime']=null;
            $JobRuns[$k]['endtime']=null;
            
            // peut etre interessant de conserver une heure GMT quelque part 
        }
        return $JobRuns;
    }

    public function Runtimes($em,$jobName,$Filter) { 
        $jobId = $this->getJobIdByName($em,$jobName);
        $JobRuns = $em->getRepository("AriiATSBundle:UjoJobRuns")->findJobRuntimes($jobId,$Filter);
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
            
            #$JobRuns[$k]['starttime'] = $JobRun['startime'] + $this->offset;
            #$JobRuns[$k]['endtime']   = $JobRun['endtime']  + $this->offset;

            $JobRuns[$k]['startime']=null;
            $JobRuns[$k]['endtime']=null;
            
            // peut etre interessant de conserver une heure GMT quelque part 
        }
        return $JobRuns;
    }
    
    public function Audit($em,$id) {
      
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
    
    public function calendarDays($em,$calendarName,$Filter) { 
        $Days = $em->getRepository("AriiATSBundle:UjoCalendar")->findDays($calendarName,$Filter);
        return $Days;
    }
    
    public function calendarJobs($em,$calendarName,$Filter) { 
        $Jobs = $em->getRepository("AriiATSBundle:UjoJobst")->findJobsByCalendar($calendarName,$Filter);
        foreach ($Jobs as $k=>$Job) {
            $Jobs[$k]['nextStart'] = new \DateTime('@'.$Job['nextStart']);
            $Jobs[$k]['nextStart']->setTimezone($this->TZ);
            $Jobs[$k]['useCalendar'] = ($Job['runCalendar']==$calendarName?'run':'exclude');
        }
        return $Jobs;
    }
    
}
