<?php

namespace Arii\ReportBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ErrorsController extends Controller{

    public function indexAction()
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();
        return $this->render('AriiReportBundle:Errors:index.html.twig', $FIlters );
    }

    public function JobsToolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render("AriiReportBundle:Errors:jobs_toolbar.xml.twig", array(), $response);
    }

    public function RunsToolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render("AriiReportBundle:Errors:runs_toolbar.xml.twig", array(), $response);
    }

    public function jobsAction()
    {        
        $portal = $this->container->get('arii_core.portal');        
        $Jobs = $this->getDoctrine()->getRepository("AriiReportBundle:JOB")->findErrors();
        $Grid = [];
        foreach ($Jobs as $Job) {
            array_push($Grid, [
                'id' => $Job->getId(),
                'job' => $Job->getJobName(),
                'app' => $Job->getApp(),
                'env' => $Job->getEnv(),
                'class' => $Job->getJobClass()
            ]);
        }
        $dhtmlx = $this->container->get('arii_core.render'); 
        return $dhtmlx->grid($Grid,'job,app,env,class');
    }

    public function runsAction()
    {        
        $portal = $this->container->get('arii_core.portal');        
        $Runs = $this->getDoctrine()->getRepository("AriiReportBundle:RUN")->findErrors();
        $Grid = [];
        foreach ($Runs as $Run) {
            array_push($Grid, [
                'id' => $Run->getId(),
                'job' => $Run->getJobName(),
                'app' => $Run->getApp(),
                'env' => $Run->getEnv()
            ]);
        }
        $dhtmlx = $this->container->get('arii_core.render'); 
        return $dhtmlx->grid($Grid,'job,app,env');
    }
    
}
?>
