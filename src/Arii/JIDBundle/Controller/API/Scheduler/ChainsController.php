<?php

namespace Arii\JIDBundle\Controller\API\Scheduler;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class ChainsController extends Controller
{

    public function listAction($repoId='ojs_db')
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();

        $em = $this->getDoctrine()->getManager($repoId);        
        $history = $this->container->get('arii_jid.history');
        $Orders = $history->Chains($em); 
        switch ($Filters['outputFormat']) {
            case 'dhtmlxGrid':
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Orders,'spoolerId,clusterMemberId,path,status','status');
                break;
            case 'dhtmlxPie':
                // Aggregation
                $Agg = [];
                foreach ($Orders as $Order) {
                    $id = $Order['status'];
                    if (!isset($Agg[$id])) {
                        $Agg[$id] = [
                            "id"    => $id,
                            "status" => $id,
                            "nb"    => 1
                        ];
                    }
                    else {
                        $Agg[$id]['nb']++;                        
                    }
                }
                $Pie = [];
                foreach ($Agg as $k=>$A ) {
                    array_push($Pie,$A);
                }
                $data = $this->get('jms_serializer')->serialize($Pie, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
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