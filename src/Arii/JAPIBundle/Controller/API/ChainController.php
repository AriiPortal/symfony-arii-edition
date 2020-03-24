<?php

namespace Arii\JAPIBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class ChainController extends Controller
{
    
    public function getAction($instanceId, $chainName, $outputFormat, Request $request)
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
        
        $XML = $this->container->get('arii_japi.get');
        $Chains = $XML->Chain($instanceId,$chainName,$Filter); 
        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Chains, 'xml');
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Chains, 'json');
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }

    public function putAction($instanceId, $chainName, Request $request)
    {
        $http = $this->container->get('arii_core.http');    
        
        $Parameters = $http->jsonData($request);
        $XML = $this->container->get('arii_japi.post');        
        $Order = $XML->putChain($instanceId,$chainName,$Parameters); 
        
        $data = $this->get('jms_serializer')->serialize($Order, 'json');
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }    
    

}