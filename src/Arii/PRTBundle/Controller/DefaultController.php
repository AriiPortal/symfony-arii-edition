<?php

namespace Arii\PRTBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AriiPRTBundle:Default:index.html.twig');
    }

    public function readmeAction()
    {
        return $this->render('AriiPRTBundle:Default:readme.html.twig');
    }

    public function menuAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        return $this->render('AriiPRTBundle:Default:menu.json.twig',array(), $response);
    }
    
    public function ribbonAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        return $this->render('AriiPRTBundle:Default:ribbon.json.twig',array(), $response);
    }

    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiPRTBundle:Default:toolbar.xml.twig', array(), $response );
    }
}
