<?php

namespace Arii\AdminBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class OracleController extends Controller{

    public function indexAction()
    {
        return $this->render('AriiAdminBundle:Oracle:index.html.twig');
    }

    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render("AriiAdminBundle:Oracle:toolbar.xml.twig", array(), $response);
    }
    
}
?>
