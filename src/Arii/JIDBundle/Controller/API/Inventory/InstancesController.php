<?php

namespace Arii\JIDBundle\Controller\API\Inventory;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class InstancesController extends Controller
{
    public function listAction($repoId='ojs_db')
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();

        $em = $this->getDoctrine()->getManager($repoId);        
        $history = $this->container->get('arii_jid.inventory');
        $Instances = $history->Instances($em); 
        switch ($Filters['outputFormat']) {
            case 'dhtmlxGrid':
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Instances,'schedulerId,hostname,port,liveDirectory,created,modified');        
                break;
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Instances, 'xml', SerializationContext::create()->setGroups(array('default')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Instances, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }
    
}