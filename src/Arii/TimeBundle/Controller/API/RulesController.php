<?php

namespace Arii\TimeBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializationContext;

class RulesController extends Controller
{

    public function listAction($outputFormat, Request $request) {
        
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
        
        $em = $this->getDoctrine()->getManager();        

        $db = $this->container->get('arii_time.db');        
        $Zones = $db->Rules($em); 

        switch ($Filters['outputFormat']) {
            case 'dhtmlx':
                $type = 'xml';
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Zones,'name,title,description,latitude,longitude');        
                break;
            default:
                $type = 'json';
                $data = $this->get('jms_serializer')->serialize($Zones, $type, SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/'.$type);
                break;
        }        
        return $response;    
    }
}