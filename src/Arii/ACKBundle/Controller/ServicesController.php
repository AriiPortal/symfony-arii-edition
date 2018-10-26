<?php

namespace Arii\ACKBundle\Controller;
use Arii\ACKBundle\Form\AlarmType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ServicesController extends Controller
{
    public function indexAction()
    {
        return $this->render('AriiACKBundle:Services:index.html.twig');
    }

    public function gridAction()
    {
        $request = Request::createFromGlobals();
        list($ok,$warning,$critical) = explode('|',$request->get('state'));

        $Services = $this->getDoctrine()->getRepository('AriiACKBundle:Service')->listStates($ok,$warning,$critical);
        $render = $this->container->get('arii_core.render');     
        return $render->grid($Services,'host_name,title,description,state,state_time','state');
    }

    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiACKBundle:Services:toolbar.xml.twig', array(), $response);
    }
        
    public function countAction()
    {
        return new Response( '{ count: "'.$this->getDoctrine()->getRepository('AriiACKBundle:Service')->getNb().'" }' );
    }
    
    public function pieAction()
    {
        $Pie = [
            'FAILURE' => 0,
            'WARNING' => 0,
            'DOWNTIME' => 0,
            'UNKNOWN' => 0
        ];

        $Chart = $this->getDoctrine()->getRepository('AriiACKBundle:Services')->pieNotOk();
        foreach($Chart as $c) {
            $k = $c['STATUS'];
            if (!isset($Pie[$k]))
                continue;
            $Pie[$k] = $c['NB'];
        }
        $render = $this->container->get('arii_core.render');     
        return $render->pie($Pie);
    }
    
    public function infoAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        
        $Probe = $this->getDoctrine()->getRepository("AriiACKBundle:Services")->find($id);
        $serializer = \JMS\Serializer\SerializerBuilder::create()->build();
                
        return $this->render('AriiACKBundle:Host:bootstrap.html.twig', $serializer->toArray($Probe) );
    }

    public function formAction()
    {   
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        
        list($Event) = $this->getDoctrine()->getRepository("AriiACKBundle:Service")->Service($id);     

        $dhtmlx = $this->container->get('arii_core.render'); 
        return $dhtmlx->form($Event);        
    }
    
}
