<?php

namespace Arii\ReportBundle\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AppsController extends Controller
{

    public function indexAction()
    {          
        $Filters = $this->container->get('report.filter')->getRequestFilter();
        return $this->render('AriiReportBundle:Dashboard\Apps:index.html.twig', $Filters );
    }

    public function gridAction()
    {
        $em = $this->getDoctrine()->getManager();
        $Apps = $em->getRepository("AriiCoreBundle:Application")->findApplications();
        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><rows>";
        foreach ($Apps as $app) {
            $xml .= '<row id="'.$app['name'].'">';
            if ($app['title']!='')
                $xml .= '<cell>'.$app['title'].'</cell>';
            else
                $xml .= '<cell>'.$app['name'].'</cell>';
            $xml .= '</row>';
        }
        $xml .= '</rows>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;
    }
    
    // Prepare un graphique
    private function DrawBar($Data) { 
        // liste triee
        asort($Data); 
        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><data>";
        foreach ($Data as $k=>$data) {
            $xml .= '<item id="'.$k.'">';
            foreach ($data as $k=>$v) {
                $xml .= '<'.$k.'>'.$v.'</'.$k.'>';
            }
            $xml .= '</item>';
        }
        $xml .= '</data>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;            
    }
    
    // Nombre de jobs par applications
    public function jobsAction()
    {
        $Filters = $this->container->get('report.filter')->getRequestFilter();

        $em = $this->getDoctrine()->getManager();
        $DBJobs = $em->getRepository("AriiReportBundle:JOBDay")->findApps($Filters['start'],$Filters['end'],$Filters['env'],$Filters['jobClass'],false);

        $portal = $this->container->get('arii_core.portal');
        $App = $portal->getApplications();

        $nb=0;
        
        $Jobs =[];
        foreach ($DBJobs as $job) {
            $a = $job['app'];
            
            // Filtre par categorie
            if (!isset($App[$a])) continue;

            $title=$App[$a]['title'];
            if ($App[$a]['active']==0) continue;            
            if ($job['jobs']==0) continue;
            
            // filtre des categories
            if (($Filters['category']!='*') and ($App[$a]['category']!=$Filters['category'])) continue;
            
            if ($nb++ >= $Filters['limit']) break;
            
            $Jobs[$a] = [
                'application' => $title,
                'jobs' =>    $job['jobs'],
                'created' => $job['created'],
                'deleted' => $job['deleted']
            ];
        }
        return $this->DrawBar($Jobs);
    }

    // Nombre d'exécution par applications
    public function runsAction ()
    {
        $Filters = $this->container->get('report.filter')->getRequestFilter();
        
        $em = $this->getDoctrine()->getManager();
        $DBRuns = $em->getRepository("AriiReportBundle:RUNDay")->findApps($Filters['start'],$Filters['end'],$Filters['env'],$Filters['jobClass']);
        
        $portal = $this->container->get('arii_core.portal');
        $App = $portal->getApplications();

        $Runs = [];
        $nb=0;
        foreach ($DBRuns as $run) {
            $a = $run['app'];
            
            if (!isset($App[$a])) continue;
            $title=$App[$a]['title'];
            if ($App[$a]['active']==0) continue;
            if ($run['runs']==0) continue;
            
            // filtre des categories
            if (($Filters['category']!='*') and ($App[$a]['category']!=$Filters['category'])) continue;
            
            if ($nb++ >= $Filters['limit']) break;
            
            $Runs[$a] = [
                'application' => $title,
                'runs' =>    $run['runs'],
            ];
        }
        return $this->DrawBar($Runs);
    }

    // Nombre d'exécution par applications
    public function alertsAction ()
    {
        $Filters = $this->container->get('report.filter')->getRequestFilter();
        
        $em = $this->getDoctrine()->getManager();
        $DBRuns = $em->getRepository("AriiReportBundle:RUNDay")->findApps($Filters['start'],$Filters['end'],$Filters['env'],$Filters['jobClass']);
        
        $portal = $this->container->get('arii_core.portal');
        $App = $portal->getApplications();

        $Runs = [];
        $nb=0;
        foreach ($DBRuns as $run) {
            $a = $run['app'];
            
            if (!isset($App[$a])) continue;
            $title=$App[$a]['title'];
            if ($App[$a]['active']==0) continue;
            if ($run['runs']==0) continue;
            
            // filtre des categories
            if (($Filters['category']!='*') and ($App[$a]['category']!=$Filters['category'])) continue;
            
            if ($nb++ >= $Filters['limit']) break;
            
            $Runs[$a] = [
                'application' => $title,
                'alarms' =>    $run['alarms'],
            ];
        }
        return $this->DrawBar($Runs);
    }
    
}

