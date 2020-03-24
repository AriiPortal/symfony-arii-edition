<?php

namespace Arii\ATSBundle\Controller\APIDB;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class EventsController extends Controller
{
    public function listAction($repoId='ats_db', $outputFormat, Request $request)
    {
        $em = $this->getDoctrine()->getManager($repoId);        
        
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
        
        $event = $this->container->get('arii_ats.Events');        
        $Events = $event->Events($em,$Filter);

        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            case 'dhtmlxGrid':
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Events,'agentName,nodeName,machStatus,Status,type,maxLoad,factor,opsys,opSys','machStatus');        
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Events, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
        
    }

    public function listJobAction($repoId='ats_db', $outputFormat, Request $request)
    {
        $em = $this->getDoctrine()->getManager($repoId);        
        
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
        
        $event = $this->container->get('arii_ats.Events');        
        $Events = $event->JobEvents($em,$Filter);

        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            case 'dhtmlxGrid':
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Events,'agentName,nodeName,machStatus,Status,type,maxLoad,factor,opsys,opSys','machStatus');        
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Events, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
        
    }
    
    public function listMachineAction($repoId='ats_db', $outputFormat, Request $request)
    {
        $em = $this->getDoctrine()->getManager($repoId);        
        
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
        
        $event = $this->container->get('arii_ats.Events');        
        $Events = $event->MachineEvents($em,$Filter);

        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            case 'dhtmlxGrid':
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Events,'agentName,nodeName,machStatus,Status,type,maxLoad,factor,opsys,opSys','machStatus');        
                break;
            case 'ics':
                $calendar = $this->container->get('arii_core.output'); 
                $response = new Response($calendar->iCal($Events,
                    [ 
                        'id' => 'eoid', 
                        'startTime' => 'eventTime',
                        'endTime' => 'eventTime',
                        'summary' => 'messageDisplay',
                        'description' => 'text'
                    ]
                ));
                $response->headers->set('Content-Type', 'text/calendar; charset=utf-8');
                $response->headers->set('Content-Dispositio', 'inline; filename=arii.ics');
                break;                
            default:
                $data = $this->get('jms_serializer')->serialize($Events, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
        
    }
    
}