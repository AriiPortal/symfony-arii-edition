<?php

namespace Arii\ATSBundle\Controller\APIDB;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class AgentsController extends Controller
{
    
    public function listAction($repoId='ats_db')
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();        

        $em = $this->getDoctrine()->getManager($repoId);        
        $state = $this->container->get('arii_ats.state');        
        $Agents = $state->Agents($em,$Filters['machine']);
        
        switch ($Filters['outputFormat']) {
            case 'dhtmlxGrid':
                $type = 'xml';
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Agents,'agentName,nodeName,machStatus,Status,type,maxLoad,factor,opsys,opSys','machStatus');        
                break;
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Agents, 'xml', SerializationContext::create()->setGroups(array('default')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Agents, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }
  
}