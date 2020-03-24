<?php

namespace Arii\ATSBundle\Controller\APIDB;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class JobsController extends Controller
{
    public function listAction($repoId='ats_db', $outputFormat, Request $request)
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
        
        $em = $this->getDoctrine()->getManager($repoId);        
        $state = $this->container->get('arii_ats.state');        
        $Jobs = $state->Jobs($em,$Filter);

        $time = time();
        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            case 'dhtmlx':
                $type = 'xml';
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Jobs,'alarmTime,alarm,jobName,stateGrid,status,theUser,eventComment,nb,stateTime,state,firstTime,description','state');        
                break;
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Jobs, 'xml', SerializationContext::create()->setGroups(array('default')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Jobs, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }

    public function getAction($repoId='ats_db',$eventId=-1 )
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();        
        if (isset($Filters['repoId'])) 
            $repoId = $Filters['repoId'];        
        
        $em = $this->getDoctrine()->getManager($repoId);        
        $state = $this->container->get('arii_ats.state');        
        list($Alarm) = $em->getRepository("AriiATSBundle:UjoAlarm")->findAlarm( $eventId );
        
        $autosys = $this->container->get('arii_ats.autosys');
        
        $xml = "<?xml version='1.0' encoding='iso-8859-1'?>";
        $xml .= '<data>';
        foreach(array('response','jobName') as $k) {
            if (isset($Alarm[$k]))
                $xml .= '<'.$k.'>'.$Alarm[$k].'</'.$k.'>';
        }
        $xml .= '<status>'.$autosys->Status($Alarm['status']).'</status>';
        $xml .= "</data>\n";
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent( $xml );
        return $response;     
    }

    public function runsAction($repoId='ats_db',$eventId=-1 )
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();        
        if (isset($Filters['repoId'])) 
            $repoId = $Filters['repoId'];        
        
        $em = $this->getDoctrine()->getManager($repoId);        
        $state = $this->container->get('arii_ats.state');        
        list($Alarm) = $em->getRepository("AriiATSBundle:UjoAlarm")->findAlarm( $eventId );
        
        $autosys = $this->container->get('arii_ats.autosys');
        
        $xml = "<?xml version='1.0' encoding='iso-8859-1'?>";
        $xml .= '<data>';
        foreach(array('response','jobName') as $k) {
            if (isset($Alarm[$k]))
                $xml .= '<'.$k.'>'.$Alarm[$k].'</'.$k.'>';
        }
        $xml .= '<status>'.$autosys->Status($Alarm['status']).'</status>';
        $xml .= "</data>\n";
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent( $xml );
        return $response;     
    }
    
}