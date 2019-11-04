<?php
/*
 * Recupere les données et fournit un tableau pour les composants DHTMLx
 */ 
namespace Arii\ReportBundle\Service;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AriiRuns
{
    public function __construct (  
            \Arii\CoreBundle\Service\AriiPortal $portal ) {
    }

/*********************************************************************
 * Runs
 *********************************************************************/    
    public function Runs($em,$Filter) {       
        $Runs = $em->getRepository("AriiReportBundle:RUN")->findRuns($Filter);
        $Done = []; // Recherche de doublons
        $New = [];
        $doublons = 0;
        foreach ($Runs as $k=>$Run) {
            $id = $Runs[$k]['job_id'].'.'.$Runs[$k]['run'].'.'.$Runs[$k]['try'];
            if (!$Runs[$k]['end_time'])
                continue;
            if (isset($Done[$id])) {
                $Done[$id]++;
                $doublons++;
                continue;
            }
            $Runs[$k]['duration'] = $Runs[$k]['start_time']->diff($Runs[$k]['end_time']);
            $Runs[$k]['runtime'] = sprintf("%02d:%02d:%02d", $Runs[$k]['duration']->format('%H'), $Runs[$k]['duration']->format('%i'), $Runs[$k]['duration']->format('%s'));
            $Done[$id]=0;
            
            array_push($New,$Runs[$k]);
        }
        return $New;
    }
}
