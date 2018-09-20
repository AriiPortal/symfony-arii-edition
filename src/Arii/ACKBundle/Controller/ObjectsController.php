<?php

namespace Arii\ACKBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ObjectsController extends Controller{

    public function indexAction()
    {
        return $this->render('AriiACKBundle:Objects:index.html.twig');
    }

    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render("AriiACKBundle:Objects:toolbar.xml.twig", array(), $response);
    }

    public function grid_menuAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiACKBundle:Objects:grid_menu.xml.twig',array(), $response );
    }
    
    public function gridAction()
    {
        $request = Request::createFromGlobals();
        $type = $request->get('type');

        $Objects = $this->getDoctrine()->getRepository('AriiACKBundle:Object')->findBy(array('obj_type' => $type), array('name' => 'ASC'), 2000);
        $Grid = [];
        foreach($Objects as $Obj) {
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
    
    public function formAction()
    {   
        $request = Request::createFromGlobals();
        $id = $request->get('id');

        $Object = $this->getDoctrine()->getRepository("AriiACKBundle:Object")->find($id);
        
        $Obj['name']        = $Object->getName();        
        $Obj['title']       = $Object->getTitle();        
        $Obj['description'] = $Object->getDescription();        
        $Obj['obj_type']    = $Object->getObjType();        
        $Obj['state']       = $Object->getState();        
        $Obj['state_time']  = $Object->getStateTime();        
        $Obj['source']      = $Object->getSource();
        $Obj['updated']     = $Object->getUpdated();        

        $dhtmlx = $this->container->get('arii_core.render'); 
        return $dhtmlx->form($Obj);        
    }

    public function deleteAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        
        $alert = $this->getDoctrine()->getRepository("AriiACKBundle:Object")->find($id);      
        if ($alert) {
            $em = $this->getDoctrine()->getManager();       
            $em->remove($alert);
            $em->flush();
            return new Response("success");            
        }
        
        return new Response("?!");            
    }
    
    public function saveAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');

        $alert = new \Arii\ACKBundle\Entity\Alarm();
        if( $id!="" )
            $alert = $this->getDoctrine()->getRepository("AriiACKBundle:Object")->find($id);      
  
        $alert->setName($request->get('name'));
        $alert->setTitle($request->get('title'));
        $alert->setDescription($request->get('description'));
        $alert->setAlarm($request->get('event'));
        $alert->setAlarmType($request->get('event_type'));

        $alert->setStartTime(new \DateTime($request->get('start_time')));
        $alert->setEndTime(new \DateTime($request->get('end_time')));

        // rattachement
        if ($request->get('application_id')!="") {
            $db = $this->getDoctrine()->getRepository("AriiACKBundle:Application")->find($request->get('application_id'));
            $alert->setApplication($db);
        }
        if ($request->get('domain_id')!="") {
            $db = $this->getDoctrine()->getRepository("AriiACKBundle:Domain")->find($request->get('domain_id'));
            $alert->setDomain($db);
        }
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($alert);
        $em->flush();        

        return new Response("success");
    }
   
    public function stateAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        $state = $request->get('state');

        $event = new \Arii\ACKBundle\Entity\Alarm();
        $event = $this->getDoctrine()->getRepository("AriiACKBundle:Object")->find($id);      
        if( !$event )
            return new Response("$id ?");
        
        $event->setState($state);
        $event->setStateTime(new \DateTime());
        
        $em = $this->getDoctrine()->getManager();        
        $em->persist($event);
        $em->flush();        

        return new Response("success");
    }

}

?>
