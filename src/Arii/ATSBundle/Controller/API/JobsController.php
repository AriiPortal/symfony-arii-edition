<?php

namespace Arii\ATSBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class JobsController extends Controller
{

    public function listAction($instanceId='ACE', $outputFormat, Request $request )
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
    
        $state = $this->container->get('arii_ats.state');
        $repoId = $state->getRepo($instanceId);
        $em = $this->getDoctrine()->getManager($repoId);             
        $Jobs = $state->Jobs($em,$Filter);

        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            default:
                $data = $this->get('jms_serializer')->serialize($Jobs, 'json');
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }
   
}