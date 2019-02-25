<?php

namespace Arii\TimeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('AriiTimeBundle:Default:index.html.twig');
    }

    public function readmeAction()
    {
        return $this->render('AriiTimeBundle:Default:readme.html.twig');
    }

    public function menuAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        
        return $this->render('AriiTimeBundle:Default:menu.xml.twig',array(), $response );
    }

    public function swaggerAction()
    {
        $portal = $this->container->get('arii_core.portal');
        return $this->render('AriiTimeBundle:Default:swagger.html.twig');
    }
        
}
