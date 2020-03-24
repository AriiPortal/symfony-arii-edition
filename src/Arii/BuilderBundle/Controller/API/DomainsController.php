<?php

namespace Arii\BuilderBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class DomainsController extends Controller
{
    public function listAction(Request $request)
    {
        $http = $this->container->get('arii_core.http');    
        
        $files = $this->container->get('arii_builder.files');
        $Domains = $files->Domains();
        
        $data = $this->get('jms_serializer')->serialize($Domains, 'json');
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
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