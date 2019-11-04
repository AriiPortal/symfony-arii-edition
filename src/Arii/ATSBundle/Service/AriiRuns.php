<?php
// src/Arii/JOCBundle/Service/AriiState.php
/*
 * Recupere les données et fournit un tableau pour les composants DHTMLx
 */ 
namespace Arii\ATSBundle\Service;

class AriiRuns
{
    protected $portal;
    protected $autosys;    
    protected $TZ;
    protected $offset;

    public function __construct (
            \Arii\CoreBundle\Service\AriiPortal $portal,
            \Arii\ATSBundle\Service\AriiAutosys $autosys ) {

        date_default_timezone_set('UTC');
        $this->portal = $portal;
        $this->autosys = $autosys;
        $this->TZ = $portal->getTimeZone();
        $this->offset = timezone_offset_get( $this->TZ, new \DateTime() );
    }

    public function Runs($em,$Filter) {      
        $JobRuns = $em->getRepository("AriiATSBundle:UjoJobRuns")->findRuns($Filter);
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
    
}
