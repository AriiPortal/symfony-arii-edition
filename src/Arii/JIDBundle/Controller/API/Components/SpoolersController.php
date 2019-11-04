<?php

namespace Arii\JIDBundle\Controller\API\Components;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class SpoolersController extends Controller
{
    public function listAction($repoId='ojs_db', $outputFormat, Request $request)
    {
        $em = $this->getDoctrine()->getManager($repoId);        
        
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
        
        $history = $this->container->get('arii_jid.Components');
        $Spoolers = $history->Spoolers($em); 
        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            case 'dhtmlxGrid':
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Spoolers,'');        
                break;
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Spoolers, 'xml', SerializationContext::create()->setGroups(array('default')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Spoolers, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }
    
}