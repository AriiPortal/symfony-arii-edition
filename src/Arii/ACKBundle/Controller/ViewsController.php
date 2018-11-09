<?php

namespace Arii\ACKBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ViewsController extends Controller{

    public function indexAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        
        return $this->render('AriiACKBundle:Views:index.html.twig', [ 'id' => $id ]);
    }

    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render("AriiACKBundle:Views:toolbar.xml.twig", array(), $response);
    }
    
    public function gridAction()
    {
        $request = Request::createFromGlobals();
        $type = $request->get('type');

        $Probes = $this->getDoctrine()->getRepository('AriiACKBundle:Probe')->findBy([], array('name' => 'ASC'), 2000);
        $Grid = [];
        foreach($Probes as $Obj) {
            array_push($Grid, [
                'id'          => $Obj->getId(),
                'name'        => $Obj->getName(),
                'description' => $Obj->getDescription(),
                'type'        => $Obj->getObjType()
            ]);
        }
        $render = $this->container->get('arii_core.render');
        return $render->grid($Grid,'name,description,type');
    }
    
    public function deleteAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        
        $view = $this->getDoctrine()->getRepository("AriiACKBundle:Graph")->find($id);      
        if ($view) {
            $em = $this->getDoctrine()->getManager();       
            $em->remove($view);
            $em->flush();
            return new Response("success");            
        }
        
        return new Response("?!");            
    }
    
    public function saveAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        
        $Graph = new \Arii\ACKBundle\Entity\Graph();
        if ($id>0)
            $Graph = $this->getDoctrine()->getRepository("AriiACKBundle:Graph")->find($id);
        
        $Graph->setName($request->get('name'));
        $Graph->setTitle($request->get('title'));
        $Graph->setDescription($request->get('description'));
        $Graph->setUpdated(new \DateTime());
       
        $em = $this->getDoctrine()->getManager();
        $em->persist($Graph);
        $em->flush();        

        return new Response("success");
    }
    
}

?>
