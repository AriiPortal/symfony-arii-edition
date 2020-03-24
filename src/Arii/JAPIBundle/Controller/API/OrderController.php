<?php

namespace Arii\JAPIBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class OrderController extends Controller
{
    
    public function getAction($instanceId,$orderId, $outputFormat, Request $request)
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
        
        $XML = $this->container->get('arii_japi.get');
        list($chainName,$orderName) = split(',',$orderId);
        $Order = $XML->Order($instanceId,$chainName,$orderName,$Filter); 
        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Order, 'xml');
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Order, 'json');
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }
    
    public function putAction($instanceId, $orderId, Request $request)
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        
        $Parameters = $http->jsonData($request);
        $XML = $this->container->get('arii_japi.post');        
        $Order = $XML->putOrder($instanceId,$orderId,$Parameters); 
        
        $data = $this->get('jms_serializer')->serialize($Order, 'json');
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        $response->setStatusCode(201);
        return $response;
    }    
    
    public function deleteAction($instanceId, $orderId, Request $request)
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        
        $Parameters = $http->Filter($request);
        $XML = $this->container->get('arii_joc.modify');        
        $Order = $XML->deleteOrder($instanceId,$orderId,$Parameters); 
        
        $data = $this->get('jms_serializer')->serialize($Order, 'json');
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }    

    public function logAction($instanceId,$orderName,$logId,$outputFormat,Request $request)
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);

        $XML = $this->container->get('arii_japi.log');
        $data = $XML->Order($instanceId,$orderName,$logId,$Filter); 
        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            case 'plain':
                $Log = [];
                foreach(explode("\n",$data) as $line) {
                    if (substr($line,0,5)!='<span') continue;
                    array_push($Log,strip_tags($line));
                }
                $response = new Response(implode("\n",$Log));
                $response->headers->set('Content-Type', 'text/plain');
                break;
           default:
                $response = new Response($data);
                $response->headers->set('Content-Type', 'text/html');
                break;                
        }
        return $response;
    }
    
}