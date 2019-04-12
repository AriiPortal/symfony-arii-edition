<?php

namespace Arii\ATSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ChangesController extends Controller
{
    public function indexAction()
    {
        $portal = $this->container->get('arii_core.portal');       
        return $this->render('AriiATSBundle:Changes:index.html.twig', [ 'db' => $portal->getDatabase() ]);
    }

}