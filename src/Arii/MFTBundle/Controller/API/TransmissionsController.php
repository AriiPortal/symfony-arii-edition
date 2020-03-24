<?php

namespace Arii\MFTBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class TransmissionsController extends Controller
{
    public function listAction($outputFormat, Request $request) {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);

        $em = $this->getDoctrine()->getManager();        
        $state = $this->container->get('arii_mft.transmissions');        
        $Transmissions = $state->Transmissions($em,$Filter);

        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            default:
                $data = $this->get('jms_serializer')->serialize($Transmissions, 'json');
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }
    
}