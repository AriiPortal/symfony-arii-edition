<?php

namespace Arii\MFTBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class DeliveriesController extends Controller
{
    public function getAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();        
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);

        $state = $this->container->get('arii_mft.Deliveries');
        $Deliveries = $state->Deliveries($em,$Filter);

        switch ($Accept['outputFormat']) {
            case 'dhtmlx':
                $type = 'xml';
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Deliveries,'alarmTime,alarm,jobName,stateGrid,status,theUser,eventComment,nb,stateTime,state,firstTime,description','state');        
                break;
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Deliveries, 'xml', SerializationContext::create()->setGroups(array('default')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Deliveries, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }

    public function flowsAction($outputFormat='', Request $request)
    {
        $em = $this->getDoctrine()->getManager();        
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);

        $state = $this->container->get('arii_mft.Deliveries');
        $Flows = $state->Flows($em,$Filter);

        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            case 'dhtmlx':
                $type = 'xml';
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Flows,'alarmTime,alarm,jobName,stateGrid,status,theUser,eventComment,nb,stateTime,state,firstTime,description','state');        
                break;
            case 'html':
                $data = $this->build_table($Flows);
                $response = new Response($data);
                $response->headers->set('Content-Type', 'text/html');
                break;
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Flows, 'xml', SerializationContext::create()->setGroups(array('default')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            case 'csv':
                $output = $this->container->get('arii_core.output');
                $response = new Response($output->Csv($Flows));
                $response->headers->set('Content-Type', 'text/plain');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Flows, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }

    function build_table($array){
        // start table
        $html = '<table>';
        // header row
        $html .= '<tr>';
        foreach($array[0] as $key=>$value){
                $html .= '<th>' . htmlspecialchars($key) . '</th>';
            }
        $html .= '</tr>';

        // data rows
        foreach( $array as $key=>$value){
            $html .= '<tr>';
            foreach($value as $key2=>$value2){
                $html .= '<td>' . htmlspecialchars($value2) . '</td>';
            }
            $html .= '</tr>';
        }

        // finish table and return it

        $html .= '</table>';
        return $html;
    }    
}