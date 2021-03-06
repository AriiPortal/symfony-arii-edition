<?php

namespace Arii\JIDBundle\Controller\API\Scheduler;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class OrdersController extends Controller
{

    public function listAction($repoId='ojs_db', $outputFormat, Request $request)
    {
        $em = $this->getDoctrine()->getManager($repoId);        

        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);

        $history = $this->container->get('arii_jid.Scheduler');
        $Orders = $history->Orders($em, $Filter); 
        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            case 'dhtmlxGrid':
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Orders,'spoolerId,jobChain,id,status,at,title,state,stateText,createdTime,modTime','status');        
                break;
            case 'dhtmlxPie':
                // Aggregation
                $Agg = [];
                foreach ($Orders as $Order) {
                    $id = $Order['status'];
                    if (!isset($Agg[$id])) {
                        $Agg[$id] = [
                            "id"    => $id,
                            "status" => $id,
                            "nb"    => 1
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
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Orders, 'xml', SerializationContext::create()->setGroups(array('default')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Orders, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }
    
    public function getAction($repoId='ojs_db')
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();
        $em = $this->getDoctrine()->getManager($repoId);        
        $history = $this->container->get('arii_jid.history');
        $Orders = $history->Order($em,$Filters['spooler'],$Filters['jobChain'],$Filters['orderId']); 
        switch ($Filters['outputFormat']) {
            case 'dhtmlxForm':
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->form($Orders,'spoolerId,orderId,jobChain,priority,state,stateText,title,createdTime,modTime,ordering,payload,runTime,initialState,orderXml,distributedNextTime,occupyingClusterMemberId,log');        
                break;
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Orders, 'xml', SerializationContext::create()->setGroups(array('default')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Orders, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }

    public function payloadAction($repoId='ojs_db')
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();
        $em = $this->getDoctrine()->getManager($repoId);        
        $history = $this->container->get('arii_jid.history');
        $Order = $history->Order($em,$Filters['spooler'],$Filters['jobChain'],$Filters['orderId']); 

        // Extraction du payload
        $tools = $this->container->get('arii_core.tools');
        $Payload = $tools->xml2array($Order['payload']);
        if (isset($Payload['sos.spooler.variable_set']['variable_attr']))
            $Vars = $Payload['sos.spooler.variable_set']['variable_attr'];
        else
            $Vars = [];
        switch ($Filters['outputFormat']) {
            case 'dhtmlxGrid':
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Vars,'name,value');    
                break;
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Vars, 'xml', SerializationContext::create()->setGroups(array('default')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Vars, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }
    
}