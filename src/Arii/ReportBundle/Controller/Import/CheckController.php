<?php

namespace Arii\ReportBundle\Controller\Import;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// Verification des donnees avant mise en base de donnees
class CheckController extends Controller
{
 
    public function __construct() {
    }
    
    public function RunHourAction($force=1,$html=0)
    {
        // par défaut
        $request = Request::createFromGlobals();
        $filter = $this->container->get('report.filter');
        list($env,$app,$day_past,$day,$month,$year,$start,$end) = $filter->getFilter(
            $request->query->get( 'env' ),
            $request->query->get( 'app' ),
            $request->query->get( 'day_past' ),                
            $request->query->get( 'day' ),
            $request->query->get( 'month' ),
            $request->query->get( 'year' )
        );

        set_time_limit(3600);
        ini_set('memory_limit', '-1');        
        if ($this->container->has('profiler'))
            $this->container->get('profiler')->disable();

        $em = $this->getDoctrine()->getManager();       
        
        $Runs = $em->getRepository("AriiReportBundle:RUN")->findRunHours($start,$end);
     
        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><rows>";
        $xml .= '<head><afterInit><call command="clearAll"/></afterInit></head>';
        foreach ($Runs as $run) {
            $xml .= '<row>';
            $xml .= '<cell>'.substr($run['start_date'],0,10).'</cell>';
            $xml .= '<cell>'.$run['start_hour'].'</cell>';
            $xml .= '<cell>'.$run['spooler_name'].'</cell>';
            $xml .= '<cell>'.$run['env'].'</cell>';
            $xml .= '<cell>'.$run['app'].'</cell>';
            $xml .= '<cell>'.$run['job_class'].'</cell>';
            $xml .= '<cell>'.$run['status'].'</cell>';
            $xml .= '<cell>'.$run['executions'].'</cell>';            
            $xml .= '<cell>'.$run['alarms'].'</cell>';
            $xml .= '<cell>'.$run['acks'].'</cell>';
            $xml .= '</row>';
        }
        $xml .= '</rows>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;            
    }
}

