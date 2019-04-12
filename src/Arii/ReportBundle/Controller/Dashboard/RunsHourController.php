<?php

namespace Arii\ReportBundle\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class RunsHourController extends Controller
{

    public function indexAction()
    {
        $Filters = $this->container->get('report.filter')->getRequestFilter();
        return $this->render('AriiReportBundle:Dashboard\Runs:hour.html.twig', $Filters );
    }

    public function statusAction($app='*',$env='P',$mode='dashboard')
    {
        $Filters = $this->container->get('report.filter')->getRequestFilter();

        $em = $this->getDoctrine()->getManager();
        $Runs = $em->getRepository("AriiReportBundle:RUNHour")->findRuns($Filters['start'],$Filters['end'],$Filters['env'],$Filters['appl'],$Filters['jobClass']);        
//        if (!$Runs)
//            throw new \Exception("Start: ".$start->format('Y-m-d')."\nEnd: ".$end->format('Y-m-d')."\nApp: ".$app."\nEnv: ".$env."\nClass: ".$jobClass);

        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><rows>";
        $xml .= '<head><afterInit><call command="clearAll"/></afterInit></head>';
        $nb=0;
        foreach ($Runs as $Run) {
            $xml .= '<row>';
            $xml .= '<cell>'.$Run['date']->format('Y-m-d').'</cell>';
            $xml .= '<cell>'.$Run['date']->format('d').'</cell>';
            $xml .= '<cell>'.$Run['hour'].'</cell>';
            if (isset($Run['env']))
                $xml .= '<cell>'.$Run['env'].'</cell>';
            else
                $xml .= '<cell/>';
            if (isset($Run['application']))
                $xml .= '<cell>'.$Run['application'].'</cell>';
            else
                $xml .= '<cell/>';
            if (isset($Run['jobClass']))
                $xml .= '<cell>'.$Run['jobClass'].'</cell>';
            else
                $xml .= '<cell/>';
            if ($Run['executions']>0)
                $xml .= '<cell>'.$Run['executions'].'</cell>';
            else
                $xml .= '<cell/>';                
            if ($Run['warnings']>0)
                $xml .= '<cell>'.$Run['warnings'].'</cell>';
            else
                $xml .= '<cell/>';                
            if ($Run['alarms']>0)
                $xml .= '<cell>'.$Run['alarms'].'</cell>';
            else
                $xml .= '<cell/>';
            $xml .= '<cell>'.$nb++.'</cell>';
            $xml .= '</row>';
        }
        $xml .= '</rows>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;            
    }
    
}

