<?php

namespace Arii\ATSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AgentsController extends Controller
{
    public function indexAction()
    {
        $portal = $this->container->get('arii_core.portal');       
        return $this->render('AriiATSBundle:Agents:index.html.twig', [ 'db' =>$portal->getDatabase() ]);
    }
 
}