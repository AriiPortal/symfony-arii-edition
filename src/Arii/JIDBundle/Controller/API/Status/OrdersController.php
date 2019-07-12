<?php

namespace Arii\JIDBundle\Controller\API\Status;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class OrdersController extends Controller
{
    
    public function listAction($repoId='ojs_db')
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();

        $em = $this->getDoctrine()->getManager($repoId);        
        $status = $this->container->get('arii_jid.status');
        $Orders = $status->SuspendedOrders($em); 
        switch ($Filters['outputFormat']) {
            case 'dhtmlxGrid':
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Orders,'spoolerId,jobChain,orderId,state,stateText,modTime','color');        
                break;
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Orders, 'xml', SerializationContext::create()->setGroups(array('default')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Orders, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }
}