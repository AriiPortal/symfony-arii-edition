<?php

namespace Arii\JIDBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class SpoolersController extends Controller
{
    public function listAction($db='ojs_db')
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();
        
        $em = $this->getDoctrine()->getManager($db);        
        $history = $this->container->get('arii_jid.DB');        
        $Spoolers = $history->Spoolers($em); 
        print_r($Spoolers);
        exit();
    }
    
}