<?php

namespace Arii\BuilderBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class ListController extends Controller
{
    public function getAction($domainName,$templateName,$listName,Request $request)
    {
        $http = $this->container->get('arii_core.http');    
        
        $portal = $this->container->get('arii_core.portal');
        $workspace = $portal->getWorkspace();
        
        $content = file_get_contents($workspace.'/Builder/'.$domainName.'/'.$templateName.'/'.$listName.'.yml');
        
        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/plain');
        return $response;
    }

    public function generateAction($domainName,$templateName,$listName,Request $request)
    {
        $build = $this->container->get('arii_builder.build');
        $document = $build->build($domainName,$templateName,$listName);
        
        $response = new Response($document);
        $response->headers->set('Content-Type', 'text/plain');
        return $response;
    }
    
}