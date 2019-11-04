<?php

namespace Arii\JIDBundle\Controller\API\Components;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class ClustersController extends Controller
{
    public function listAction($repoId='ojs_db', $outputFormat, Request $request)
    {
        $em = $this->getDoctrine()->getManager($repoId);        
        
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
        
        $history = $this->container->get('arii_jid.Components');
        $Clusters = $history->Clusters($em,$Filter); 
        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            case 'dhtmlxGrid':
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Clusters,'schedulerId,lastHeartBeat,delay,status,active,dead');        
                break;
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Clusters, 'xml', SerializationContext::create()->setGroups(array('default')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Clusters, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }

    public function getAction($repoId='ojs_db',$schedulerId)
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();
        
        $em = $this->getDoctrine()->getManager($repoId);        
        $history = $this->container->get('arii_jid.history');
        $Members = $history->ClusterMembers($em,$schedulerId); 
        switch ($Filters['outputFormat']) {
            case 'dhtmlxGrid':
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Members,'schedulerId,memberId,lastHeartBeat,delay,nextHeartBeat,active,dead,command,httpUrl,deactivatingMemberId,xml,host,tcp_port,distributed_orders,backup_precedence,running_since,version');
                break;
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Members, 'xml', SerializationContext::create()->setGroups(array('default')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Members, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }
    
}