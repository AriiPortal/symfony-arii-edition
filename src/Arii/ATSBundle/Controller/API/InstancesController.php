<?php

namespace Arii\ATSBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class InstancesController extends Controller
{

    public function listAction($outputFormat, Request $request )
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
    
        $Config = $this->container->getParameter('ats');
        $Instances = $Config['instances'];
        
        // on remet sous une forme de liste 
        $Result=[];
        foreach ($Instances as $k=>$Instance) {
            array_push($Result,
                [
                    "instanceName" => $k,
                    "title" => $Instance['title'],
                    "event_server_1" => (isset($Instance['event_servers'][0])?$Instance['event_servers'][0]:null),
                    "event_server_2" => (isset($Instance['event_servers'][1])?$Instance['event_servers'][1]:null)
                ]
            );
        }    
        if (empty($Result)) {
            $response = new Response('No Content');
            $response->setStatusCode(Response::HTTP_NO_CONTENT);
            return $response;
        }
        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            default:
                $data = $this->get('jms_serializer')->serialize($Result, 'json');
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }
   
}