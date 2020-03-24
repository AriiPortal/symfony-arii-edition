<?php

namespace Arii\ATSBundle\Controller\APIDB;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class BoxesController extends Controller
{
    public function listAction($repoId='ats_db', $outputFormat, Request $request)
    {
        $em = $this->getDoctrine()->getManager($repoId);        
        
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);

        $state = $this->container->get('arii_ats.state');        
        $Boxes = $state->Boxes($em,$Filter);

        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            case 'dhtmlxGrid':
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Boxes,'role,hostname,haStatusId,statusTime,pid');        
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Boxes, 'json', SerializationContext::create());
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;        
    }
   
}