<?php
namespace Arii\ReportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller
{
    public function __construct( )
    {
    }

    public function indexAction()
    {
        $request = Request::createFromGlobals();        
        $job_name = $request->get('job_name');
        return $this->render('AriiReportBundle:Search:index.html.twig', [ 'job_name' => $job_name ]);
    }

    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiReportBundle:Search:toolbar.xml.twig',array(),$response  );
    }
    
    public function gridAction()
    {
        $request = Request::createFromGlobals();        
        $Jobs = $this->getDoctrine()->getRepository("AriiReportBundle:JOB")->findJob(
            trim($request->get('job_name')),
            trim($request->get('description')),                
            trim($request->get('command'))
        );
        
        $render = $this->container->get('arii_core.render');     
        return $render->grid($Jobs,'job_name,description,command');
    }

    public function historyAction()
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();

        // tous les jobs non supprimés dans la période
        $em = $this->getDoctrine()->getManager(); 
        $History = $this->getDoctrine()->getRepository("AriiReportBundle:RUN")->findJobById(
            $Filters['id'],
            $Filters['start'],
            $Filters['end'] );
        $Data = [];
        $portal = $this->container->get('arii_core.portal');
        $ColorStatus = $portal->getColors();
        foreach ($History as $H) {

            $status = $H->getStatus();
            if (!in_array($status,['SUCCESS','FAILURE','TERMINATED'])) 
                continue;
            if (!$H->getEndTime() or !$H->getStartTime())
                continue;
            $timestamp = $H->getStartTime()->getTimestamp();
            $duration = $H->getEndTime()->getTimestamp() - $timestamp;
            if ($duration<0)
                continue;
            array_push($Data,[
                'id' => $H->getId(),
                'start_time' => $H->getStartTime(),
                'end_time' => $H->getEndTime(),
                'status' => $H->getStatus()
            ]);
        }
        $render = $this->container->get('arii_core.render');     
        return $render->grid($Data,'start_time,end_time,status','status');
    }

    public function chartAction()
    {
        $request = Request::createFromGlobals();        
        $History = $this->getDoctrine()->getRepository("AriiReportBundle:RUN")->findBy( [
            'job' => $request->get('id')
        ], [ 'end_time' => 'ASC' ] );

        $Data = [];
        $portal = $this->container->get('arii_core.portal');
        $ColorStatus = $portal->getColors();
        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><data>";
        $last_year = 0;
        $last_month = 0;
        foreach ($History as $H) {
            $status = $H->getStatus();
            if (!in_array($status,['SUCCESS','FAILURE','TERMINATED'])) 
                continue;
            if (!$H->getEndTime() or !$H->getStartTime())
                continue;
            $timestamp = $H->getStartTime()->getTimestamp();
            $duration = $H->getEndTime()->getTimestamp() - $timestamp;
            if ($duration<0)
                continue;
            $xml .= '<item id="'.$H->getId().'">';
            $xml .= '<day>'.$timestamp.'</day>';
            $xml .= '<duration>'.$duration.'</duration>';
            $xml .= '<date>'.$H->getStartTime()->format('Y-m-d').'</date>';
            $xml .= '<time>'.$H->getStartTime()->format('H:i:s').'</time>';
            $month = $H->getStartTime()->format('m');
            if ($month!=$last_month) {
                $xml .= '<month>'.($month*1).'</month>';
                $last_month = $month;
            }
            else 
                $xml .= '<month/>';
            $year = $H->getStartTime()->format('y');
            if ($year!=$last_year) {
                $xml .= '<year>'.($year*1).'</year>';
                $last_year = $year;
            }
            else 
                $xml .= '<year/>';
            $xml .= '<color>'.(isset($ColorStatus[$status])?$ColorStatus[$status]['bgcolor']:'#FFFFFF').'</color>';         
            $xml .= '</item>';            
        }
        $xml .= '</data>';
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;           
    }
    
}