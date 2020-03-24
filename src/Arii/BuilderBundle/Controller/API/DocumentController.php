<?php

namespace Arii\BuilderBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class DocumentController extends Controller
{
    public function getAction($domainName, Request $request)
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
        
        $build = $this->container->get('arii_builder.build');
        $document = $build->build($domainName,$Filter['templateName'],$Filter['listName'],$Filter);
        
        $response = new Response($document);
        $response->headers->set('Content-Type', 'text/plain');
        return $response;
    }
    
}