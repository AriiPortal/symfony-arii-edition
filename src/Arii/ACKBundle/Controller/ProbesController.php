<?php

namespace Arii\ACKBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ProbesController extends Controller{

    public function indexAction()
    {
        return $this->render('AriiACKBundle:Probes:index.html.twig',['probe' => 0]);
    }

    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render("AriiACKBundle:Probes:toolbar.xml.twig", array(), $response);
    }

    public function filterAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render("AriiACKBundle:Probes:filter.xml.twig", array(), $response);
    }
    
    public function grid_menuAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiACKBundle:Probes:grid_menu.xml.twig',array(), $response );
    }
    
    public function gridAction()
    {
        $request = Request::createFromGlobals();
        $name  = str_replace('*','%',$request->get('name'));
        $description = str_replace('*','%',$request->get('description'));;
        $type  = $request->get('type');
        
        $Probes = $this->getDoctrine()->getRepository('AriiACKBundle:Probe')->findByFilter($name,$description,$type);
        $Grid = [];
        foreach($Probes as $Obj) {
            array_push($Grid, [
                'id'          => $Obj->getId(),
                'name'        => $Obj->getName(),
                'description' => $Obj->getDescription(),
                'type'        => $this->ImgType($Obj->getObjType())
            ]);
        }
        $render = $this->container->get('arii_core.render');
        return $render->grid($Grid,'name,description,type');
    }
    
    private function ImgType($type) {
        switch($type) {
            case 1:
                return '/images/server.png';
            case 2:
                return '/images/service.png';
            case 3:
                return '/images/job.png';
            default:
                return '/images/unknown.png';
        }
    }
    
    public function formAction()
    {   
        $request = Request::createFromGlobals();
        $id = $request->get('id');

        $Probe = $this->getDoctrine()->getRepository("AriiACKBundle:Probe")->find($id);
        
        $Obj['name']        = $Probe->getName();        
        $Obj['title']       = $Probe->getTitle();        
        $Obj['description'] = $Probe->getDescription();        
        $Obj['obj_type']    = $Probe->getObjType();        
        $Obj['state']       = $Probe->getState();        
        $Obj['state_time']  = $Probe->getStateTime();        
        $Obj['source']      = $Probe->getSource();
        $Obj['updated']     = $Probe->getUpdated();        

        $dhtmlx = $this->container->get('arii_core.render'); 
        return $dhtmlx->form($Obj);        
    }

    public function deleteAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        
        $alert = $this->getDoctrine()->getRepository("AriiACKBundle:Probe")->find($id);      
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

        $probe = new \Arii\ACKBundle\Entity\Probe();
        if($id!="" )
            $probe = $this->getDoctrine()->getRepository("AriiACKBundle:Probe")->find($id);      
        
        $probe->setName($request->get('name'));
        $probe->setTitle($request->get('title'));
        $probe->setDescription($request->get('description'));

        $probe->setObjType($request->get('obj_type'));
        $probe->setSource($request->get('source'));
/*
        $probe->setState($request->get('state'));
        $probe->setStateComment($request->get('state_comment'));
        $probe->setStateTime($request->get('state_time'));
        $probe->setStateUser($request->get('state_user'));
        $probe->setStateEnd($request->get('state_end'));

        $probe->setAck($request->get('ack'));
        $probe->setAckComment($request->get('ack_comment'));
        $probe->setAckTime($request->get('ack_time'));
        $probe->setAckUser($request->get('ack_user'));
        $probe->setAckEnd($request->get('ack_end'));
        
        $probe->setDowntime($request->get('downtime'));
        $probe->setDowntimeComment($request->get('downtime_comment'));
        $probe->setDowntimeTime($request->get('downtime_time'));
        $probe->setDowntimeUser($request->get('downtime_user'));
        $probe->setDowntimeEnd($request->get('downtime_end'));
       
        $probe->setStatus($request->get('status'));
        $probe->setStatusComment($request->get('status_comment'));
        $probe->setStatusTime($request->get('status_time'));
        $probe->setStatusUser($request->get('status_user'));
*/         
        $probe->setUpdated(new \DateTime());
       
        $em = $this->getDoctrine()->getManager();
        $em->persist($probe);
        $em->flush();        

        return new Response("success");
    }
   
    public function stateAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        $state = $request->get('state');

        $event = $this->getDoctrine()->getRepository("AriiACKBundle:Probe")->find($id);      
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
