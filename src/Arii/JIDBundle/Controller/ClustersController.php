<?php

namespace Arii\JIDBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ClustersController extends Controller
{
    public function indexAction()
    {
        // database courante
        $portal = $this->container->get('arii_core.portal');       
        return $this->render('AriiJIDBundle:Clusters:index.html.twig', [ 'db' =>$portal->getDatabase() ]);
    }
}
