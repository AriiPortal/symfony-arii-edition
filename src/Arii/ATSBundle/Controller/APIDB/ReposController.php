<?php

namespace Arii\ATSBundle\Controller\APIDB;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class ReposController extends Controller
{
    public function listAction()
    {
        // Peu importe la configuration, on ne récupère que les informations fonctionnelle
        $portal = $this->container->get('arii_core.portal');
        $Repos = $portal->getRepos([ 'type' => 'ats']);
        
        $data = $this->get('jms_serializer')->serialize($Repos, 'json');

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;    
    }
    
}