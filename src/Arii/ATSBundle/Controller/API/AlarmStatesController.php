<?php

namespace Arii\ATSBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class AlarmStatesController extends Controller
{
    public function listAction($instanceId='ACE', $outputFormat, Request $request)
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);        
        
        # DateTime ?
        if (isset($Filter['alarmTime'])) {
            # ouvrir un ticket au support pour connaitre le format
            $format = "d.m.y H:i";
            $Filter['alarmTime'] = \DateTime::createFromFormat($format, $Filter['alarmTime']);
        }

        # On retouve le bon repo
        $state = $this->container->get('arii_ats.state');        
        $repoId = $state->getRepo($instanceId);
        $em = $this->getDoctrine()->getManager($repoId);             
        $Alarms = $state->Alarms($em,$Filter);

        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            default:
                $data = $this->get('jms_serializer')->serialize($Alarms, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }

    public function infosAction($repoId='ats_db')
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();        

        $em = $this->getDoctrine()->getManager($repoId);        
        $state = $this->container->get('arii_ats.state');        
        $Alarms = $state->Alarms($em);

        $state = $status = '';
        // Aggregations
        foreach ($Alarms as $Alarm) {
            $state = $Alarm['state'];
            if (isset($State[$state])) {
                $State[$state]++;
            }
            else {
                $State[$state] = 1;
            }
            $status = $Alarm['status'];
            if (isset($Status[$status])) {
                $Status[$status]++;
            }
            else {
                $Status[$status] = 1;
            }
        }
        $State['NOT_CLOSED'] = 0;
        foreach ([ 'OPEN', 'ACKNOWLEDGED'] as $v) {
            $State['NOT_CLOSED'] += (isset($State[$v])?$State[$v]:0);
        }
        $data = $this->get('jms_serializer')->serialize( 
        [
            'state'  => $state,
            'status' => $status
        ], 'json', SerializationContext::create()->setGroups(array('list')));
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
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

    
}