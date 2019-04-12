<?php

namespace Arii\ATSBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class AlarmsController extends Controller
{
    public function listAction($repoId='ats_db')
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();        
        if (isset($Filters['repoId'])) 
            $repoId = $Filters['repoId'];

        $em = $this->getDoctrine()->getManager($repoId);        
        $state = $this->container->get('arii_ats.state2');        
        $Alarms = $state->Alarms($em);

        $time = time();
        switch ($Filters['outputFormat']) {
            case 'dhtmlx':
                $type = 'xml';
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Alarms,'alarmTime,alarm,jobName,stateGrid,status,theUser,eventComment,nb,stateTime,state,firstTime,description','state');        
                break;
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Alarms, 'xml', SerializationContext::create()->setGroups(array('default')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            case 'dhtmlxPie':
                // Aggregation
                $Agg = [];
                foreach ($Alarms as $Alarm) {
                    $state = $Alarm['status'];
                    $nb    = $Alarm['nb'];
                    $id = $Alarm['alarm'];
                    if (!isset($Agg[$id])) {
                        $Agg[$id] = [
                            "id"    => $id,
                            "state" => $state,
                            "nb"    => 1,
                            "color" => $Alarm['color'],
                            "timestamp" => $time
                        ];
                    }
                    else {
                        $Agg[$id]['nb']++;                        
                    }
                }
                $Pie = [];
                foreach ($Agg as $k=>$A ) {
                    array_push($Pie,$A);
                }
                $data = $this->get('jms_serializer')->serialize($Pie, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
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
        if (isset($Filters['repoId'])) 
            $repoId = $Filters['repoId'];

        $em = $this->getDoctrine()->getManager($repoId);        
        $state = $this->container->get('arii_ats.state2');        
        $Alarms = $state->Alarms($em);

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
            'state'  => $State,
            'status' => $Status
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
        $state = $this->container->get('arii_ats.state2');        
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