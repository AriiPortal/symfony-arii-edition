<?php

namespace Arii\JAPIBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class OrdersController extends Controller
{
    public function listAction($instanceId, $outputFormat, Request $request)
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
        
        $XML = $this->container->get('arii_japi.state');
        $Spoolers = $XML->Orders($instanceId,$Filter); 
        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Spoolers, 'xml');
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Spoolers, 'json');
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }
 
    public function postAction($instanceId, Request $request)
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        
        $Parameters = $http->jsonData($request);
        $XML = $this->container->get('arii_japi.post');        
        $Order = $XML->createOrder($instanceId,$Parameters); 
        
        $data = $this->get('jms_serializer')->serialize($Order, 'json');
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }    
    
}