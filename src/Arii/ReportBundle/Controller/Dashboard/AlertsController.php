<?php

namespace Arii\ReportBundle\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AlertsController extends Controller
{
    public function indexAction()
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();
        return $this->render('AriiReportBundle:Dashboard\Alerts:index.html.twig', $Filters );
    }
    
    public function gridAction($app='*',$env='P')
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();

        $em = $this->getDoctrine()->getManager();
        $Runs = $em->getRepository("AriiReportBundle:RUNHour")->findAlerts($Filters['start'],$Filters['end'],$Filters['env'],$Filters['appl'],$Filters['jobClass']);        

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
            if ($Run['alarms']>0)
                $xml .= '<cell>'.$Run['alarms'].'</cell>';
            else
                $xml .= '<cell/>';
            $xml .= '</row>';
        }
        $xml .= '</rows>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;            
    }
    
}

