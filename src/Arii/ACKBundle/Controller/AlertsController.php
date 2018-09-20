<?php

namespace Arii\ACKBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AlertsController extends Controller{

    public function indexAction()
    {
        return $this->render('AriiACKBundle:Alerts:index.html.twig');
    }

    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render("AriiACKBundle:Alerts:toolbar.xml.twig", array(), $response);
    }

    public function gridAction()
    {
        $request = Request::createFromGlobals();
        $state = $request->get('state');
        
        $Errors = $this->getDoctrine()->getRepository('AriiACKBundle:Alert')->listState($state);
        $render = $this->container->get('arii_core.render');     
        return $render->grid($Errors,'name,title,status');
    }
    
    public function formAction()
    {   
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        
/* DHTMLX
        $sql = $this->container->get('arii_core.sql');        
        $qry = $sql->Select(array("ID","NAME","TITLE","DESCRIPTION","EVENT","START_TIME","END_TIME"))
                .$sql->From(array("ARII_EVENT"))
                .$sql->Where(array("ID"=>$id));
        
        $db = $this->container->get('arii_core.db');
        $data = $db->Connector('form');
        $data->render_sql($qry,"ID","ID,NAME,TITLE,DESCRIPTION,EVENT,START_TIME,END_TIME");
*/
        list($Event) = $this->getDoctrine()->getRepository("AriiACKBundle:Alert")->Event($id);      
        $Event['start_time'] = $Event['start_time']->format('Y-m-d H:i:s');
        $Event['end_time'] = $Event['end_time']->format('Y-m-d H:i:s');

        $dhtmlx = $this->container->get('arii_core.render'); 
        return $dhtmlx->form($Event);        
    }

    public function deleteAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        
        $alert = $this->getDoctrine()->getRepository("AriiACKBundle:Alert")->find($id);      
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

        $alert = new \Arii\ACKBundle\Entity\Event();
        if( $id!="" )
            $alert = $this->getDoctrine()->getRepository("AriiACKBundle:Alert")->find($id);      
  
        $alert->setName($request->get('name'));
        $alert->setTitle($request->get('title'));
        $alert->setDescription($request->get('description'));
        $alert->setEvent($request->get('event'));
        $alert->setEventType($request->get('event_type'));

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

        $event = new \Arii\ACKBundle\Entity\Event();
        $event = $this->getDoctrine()->getRepository("AriiACKBundle:Alert")->find($id);      
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
