<?php

namespace Arii\JIDBundle\Controller\API;

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
        $history = $this->container->get('arii_jid.history');
        $Orders = $history->Orders($em); 
        switch ($Filters['outputFormat']) {
            case 'dhtmlxGrid':
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Orders,'spoolerId,orderId,jobChain,state,endTime,runtime','status');        
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

    public function getAction($repoId='ojs_db',$orderId)
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();

        $em = $this->getDoctrine()->getManager($repoId);        
        $history = $this->container->get('arii_jid.history');
        $Orders = $history->Order($em,$orderId); 
        switch ($Filters['outputFormat']) {
            case 'dhtmlxForm':
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->form($Orders,'spoolerId,orderId,jobChain,state,stateText,startTime,endTime,runtime');        
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

    public function stepsAction($repoId='ojs_db',$orderId)
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();

        $em = $this->getDoctrine()->getManager($repoId);        
        $history = $this->container->get('arii_jid.history');
        $Steps = $history->OrderSteps($em,$orderId); 
        switch ($Filters['outputFormat']) {
            case 'dhtmlxGrid':
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Steps,'step,state,startTime,endTime,error,errorCode,errorText','status');        
                break;
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Steps, 'xml', SerializationContext::create()->setGroups(array('default')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            case 'svg':
            case 'png':
                return $this->Graph($Steps,$Filters['outputFormat']);
                break;            
            default:
                $data = $this->get('jms_serializer')->serialize($Steps, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }    
    
    public function Graph($Steps,$output='svg') {
        $graphviz = $this->container->get('arii_core.graphviz');
        
        // Design
        $out = 'digraph ACK {
fontname=arial 
fontsize=10
node [shape=plaintext,fontname=arial,fontsize=10]
edge [fontname=arial,fontsize=8,decorate=true,compound=true]
bgcolor=white
imagescale=false
rankdir=LR
';
        $last = '';
        foreach ($Steps as $k=>$Step) {
            $out .= $Step["state"];
            if ($Step["error"]>0)
                $out .= " [fillcolor=lightpink1,style=filled]\n";
            else 
                $out .= " [fillcolor=darkseagreen2,style=filled]\n";
            if ($last != '') {
                $out .= '"'.$last.'" -> "'.$Step["state"].'" [label="'.$Step["step"].'"]'."\n";
            }
            $last = $Step["state"];
        }
        $out .= "}";
        
        $graphviz = $this->container->get('arii_core.graphviz');
        $Options = array(
            'digraph' => false,
            'output' => $output
        );

        return $graphviz->dot($out,$Options);
    }
}