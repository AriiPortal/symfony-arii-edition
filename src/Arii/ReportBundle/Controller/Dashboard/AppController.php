<?php

namespace Arii\ReportBundle\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AppController extends Controller
{

    public function indexAction()
    {          
        $Filters = $this->container->get('report.filter')->getRequestFilter();
        return $this->render('AriiReportBundle:Dashboard\App:index.html.twig', $Filters );
    }

    public function jobsAction()
    {     
        $Filters = $this->container->get('report.filter')->getRequestFilter();
        $Parameters = [
            'repository' => "AriiReportBundle:JOBDay",
            'start'      => $Filters['start']->format('Y-m-d H:i:s'),
            'end'        => $Filters['end']->format('Y-m-d H:i:s'),
            'env'        => $Filters['env'],
            'app'        => $Filters['appl'],
            'class'      => $Filters['jobClass']
        ];

        $em = $this->getDoctrine()->getManager(); 
        $Jobs = $em->getRepository("AriiReportBundle:JOBDay")->findJobs($Filters['start'],$Filters['end'],$Filters['env'],$Filters['appl'],$Filters['jobClass'],false);

        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><data>";
        // Cas ou le job class est précisé
        $last = $n = 0;
        foreach ($Jobs as $Job) {
            $env = (isset($Job['env'])?$Job['env']:$Filters['env']);
            $app = (isset($Job['app'])?$Job['app']:$Filters['appl']);
            $jobClass = (isset($Job['jobClass'])?$Job['jobClass']:$Filters['jobClass']);
            $xml .= '<item id="'.$env.'|'.$app.'|'.$jobClass.'|'.$Job['date']->format('md').'">';
            $xml .= '<app>'.$app.'</app>';
            $xml .= '<env>'.$env.'</env>';
            $xml .= '<jobClass>'.$jobClass.'</jobClass>';
            $xml .= '<mois>'.$this->get('translator')->trans('month.'.($Job['date']->format('m'))*1).'</mois>';
            $xml .= '<jour>'.$Job['date']->format('d').'</jour>';
            $xml .= '<jobs>'.$Job['jobs'].'</jobs>';
            $xml .= '<created>'.$Job['created'].'</created>';
            $xml .= '<deleted>'.$Job['deleted'].'</deleted>';
            $xml .= '</item>';
            $last = $Job['jobs'];
            $n++;
        }

        $xml .= '</data>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;            
    }

}

