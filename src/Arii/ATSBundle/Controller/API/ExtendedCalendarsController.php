<?php

namespace Arii\ATSBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class ExtendedCalendarsController extends Controller
{
    public function listAction($repoId='ats_db', $outputFormat, Request $request)
    {
        $em = $this->getDoctrine()->getManager($repoId);        
        
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);

        $state = $this->container->get('arii_ats.state');        
        $Calendars = $state->ExtendedCalendars($em,$Filter);

        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            case 'dhtmlxGrid':
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Calendars,'agentName,nodeName,machStatus,Status,type,maxLoad,factor,opsys,opSys','machStatus');        
                break;
            case 'ics':
                $calendar = $this->container->get('arii_core.output'); 
                $response = new Response($calendar->iCal($Calendars,
                    [
                        'startTime' => 'day',
                        'summary' => 'calendarName'
                    ]
                ));
                $response->headers->set('Content-Type', 'text/calendar; charset=utf-8');
                $response->headers->set('Content-Dispositio', 'inline; filename=arii.ics');
                break;                
            default:
                $data = $this->get('jms_serializer')->serialize($Calendars, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
        
    }
    
    public function listDaysAction($repoId='ats_db', $outputFormat, Request $request)
    {
        $em = $this->getDoctrine()->getManager($repoId);        
        
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);

        $state = $this->container->get('arii_ats.state');        
        $Calendars = $state->Days($em,$Filter);

        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            case 'dhtmlxGrid':
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Calendars,'agentName,nodeName,machStatus,Status,type,maxLoad,factor,opsys,opSys','machStatus');        
                break;
            case 'ics':
                $calendar = $this->container->get('arii_core.output'); 
                $response = new Response($calendar->iCal($Calendars,
                    [
                        'startTime' => 'day',
                        'summary' => 'calendarName'
                    ]
                ));
                $response->headers->set('Content-Type', 'text/calendar; charset=utf-8');
                $response->headers->set('Content-Dispositio', 'inline; filename=arii.ics');
                break;                
            default:
                $data = $this->get('jms_serializer')->serialize($Calendars, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;        
    }
    
}