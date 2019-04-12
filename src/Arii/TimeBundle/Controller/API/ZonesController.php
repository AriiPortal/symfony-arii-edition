<?php

namespace Arii\TimeBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializationContext;

class ZonesController extends Controller
{

    public function listAction() {
        
        $Filters = $this->container->get('arii.filter')->getRequestFilter();
        
        $em = $this->getDoctrine()->getManager();        
        $db = $this->container->get('arii_time.db');        
        $Zones = $db->Zones($em); 

        switch ($Filters['outputFormat']) {
            case 'dhtmlx':
                $type = 'xml';
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Zones,'name,title,description,latitude,longitude');        
                break;
            case 'xml':
                $type = 'xml';
                $data = $this->get('jms_serializer')->serialize($Zones, $type, SerializationContext::create()->setGroups(array('list')));
                break;
            default:
                $type = 'json';
                $data = $this->get('jms_serializer')->serialize($Zones, $type, SerializationContext::create()->setGroups(array('list')));
                break;
        }
        
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/'.$type);
        return $response;    
    }
}