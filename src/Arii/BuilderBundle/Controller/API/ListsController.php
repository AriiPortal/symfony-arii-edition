<?php

namespace Arii\BuilderBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class ListsController extends Controller
{
    public function listAction($domainName, Request $request)
    {
        $http = $this->container->get('arii_core.http');    
        
        $files = $this->container->get('arii_builder.files');
        $Lists = $files->Lists($domainName);
        
        $data = $this->get('jms_serializer')->serialize($Lists, 'json');
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
    
}