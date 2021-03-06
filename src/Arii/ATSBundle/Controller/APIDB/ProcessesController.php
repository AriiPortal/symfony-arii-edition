<?php

namespace Arii\ATSBundle\Controller\APIDB;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class ProcessesController extends Controller
{
    public function listAction($repoId='ats_db')
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();        

        $em = $this->getDoctrine()->getManager($repoId);        
        $state = $this->container->get('arii_ats.state');        
        $Processes = $state->Processes($em);

        switch ($Filters['outputFormat']) {
            case 'dhtmlxGrid':
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Processes,'jobName,description,status,statusTime,lastStart,lastEnd,nextStart','status');        
                break;
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Processes, 'xml', SerializationContext::create()->setGroups(array('default')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Processes, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;        
    }
   
}