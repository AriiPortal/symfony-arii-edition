<?php

namespace Arii\JIDBundle\Controller\API;

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
        $Databases = $portal->getDatabases();
        $Repos = [];
        foreach ($Databases as $k=>$v) {
            if (substr($k,0,4)=='ojs_') {
                $Repos[$k] = [
                    'title' => $Databases[$k]['title'],
                    'description' => $Databases[$k]['description']
                ];
            }
        }
        $data = $this->get('jms_serializer')->serialize($Repos, 'json', SerializationContext::create()->setGroups(array('list')));

        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        return $response;    
    }
    
}