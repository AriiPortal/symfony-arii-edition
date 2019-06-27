<?php

namespace Arii\ReportBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class RulesController extends Controller{

    public function indexAction()
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();        
        return $this->render('AriiReportBundle:Rules:index.html.twig', $Filters);
    }

    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render("AriiReportBundle:Rules:toolbar.xml.twig", array(), $response);
    }

    public function selectAction()
    {
        $request = Request::createFromGlobals();
        
        $portal = $this->container->get('arii_core.portal');        
        $Rules = $portal->getRules(); 
        
        $dhtmlx = $this->container->get('arii_core.render'); 
        return $dhtmlx->select($Rules);
    }
    
    public function gridAction()
    {        
        $portal = $this->container->get('arii_core.portal');        
        $Rules = $this->getDoctrine()->getRepository("AriiReportBundle:JOBRule")->findAll( ['priority'=>'ASC'] );
        $Grid = [];
        foreach ($Rules as $Rule) {
            array_push($Grid, [
                'id' => $Rule->getId(),
                'input' => $Rule->getInput(),
                'priority' => $Rule->getPriority(),
                'output' => $Rule->getOutput(),
                'description' => $Rule->getDescription()
            ]);
        }
        $dhtmlx = $this->container->get('arii_core.render'); 
        return $dhtmlx->grid($Grid,'priority,input,output,description');
    }
    
    public function formAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        
        $Rule = $this->getDoctrine()->getRepository("AriiReportBundle:JOBRule")->find($id);
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');    
        $form = '<data>';
        if ($Rule) {
            $form .= '<id>'.$Rule->getId().'</id>';
            $form .= '<input><![CDATA['.$Rule->getInput().']]></input>';
            $form .= '<priority>'.$Rule->getPriority().'</priority>';
            $form .= '<output><![CDATA['.$Rule->getOutput().']]></output>';
            $form .= '<description><![CDATA['.$Rule->getDescription().']]></description>';            
        }
        $form .= '</data>';
        $response->setContent( $form );
        return $response;        
    }

    public function deleteAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');

        $rule = $this->getDoctrine()->getRepository("AriiReportBundle:JOBRule")->find($id);
        if (!$rule)
            return new Response("$id ?!");
        
        $em = $this->getDoctrine()->getManager();
        $em->remove($rule);
        $em->flush();  
        
        return new Response("success");
    }

    public function saveAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        
        $rule = new \Arii\ReportBundle\Entity\JOBRule();
        if( $id != "" )
            $rule = $this->getDoctrine()->getRepository("AriiReportBundle:JOBRule")->find($id);

        $rule->setInput($request->get('input'));
        $rule->setPriority($request->get('priority'));
        $rule->setOutput($request->get('output'));
        $rule->setDescription($request->get('description'));
        
        $em = $this->getDoctrine()->getManager();
        $em->persist($rule);
        $em->flush();

        return new Response("success");
    }
    
}
?>
