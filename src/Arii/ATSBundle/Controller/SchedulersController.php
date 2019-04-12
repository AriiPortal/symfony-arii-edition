<?php

namespace Arii\ATSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class SchedulersController extends Controller
{
    public function indexAction()
    {
        // database courante
        $portal = $this->container->get('arii_core.portal');       
        return $this->render('AriiATSBundle:Schedulers:index.html.twig', [ 'db' =>$portal->getDatabase() ]);
    }
}