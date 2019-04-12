<?php

namespace Arii\JIDBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class SpoolersController extends Controller
{
    public function listAction($repoId='ojs_db')
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();
        
        $em = $this->getDoctrine()->getManager($repoId);        
        $history = $this->container->get('arii_jid.history');
        $Spoolers = $history->Spoolers($em); 
        print_r($Spoolers);
        exit();
    }
    
}