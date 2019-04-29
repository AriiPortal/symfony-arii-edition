<?php

namespace Arii\JIDBundle\Controller\Scheduler;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class TasksController extends Controller
{

    public function indexAction()
    {
        // database courante
        $portal = $this->container->get('arii_core.portal');        
        return $this->render('AriiJIDBundle:Scheduler\Tasks:index.html.twig', [ 'db' =>$portal->getDatabase() ]);
    }
    
}
