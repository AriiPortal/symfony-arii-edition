<?php

namespace Arii\ReportBundle\Controller\Requests;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class RequestsController extends Controller
{
    public function indexAction()
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();
        return $this->render('AriiReportBundle:Requests:index.html.twig', $Filters);
    }

    public function toolbarAction()
    {        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');    
        return $this->render('AriiReportBundle:Requests:toolbar.xml.twig',array(), $response );
    }

    public function treeAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $list = '<?xml version="1.0" encoding="UTF-8"?>';
        $list .= '<tree id="0">
                    <item id="runtimes" text="Runtimes"/>
                 </tree>';

        $response->setContent( $list );
        return $response;        
    }

    public function listAction()
    {
        $lang = $this->getRequest()->getLocale();
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        # Appel du service
        $requests = $this->container->get('arii_core.requests');       
        $response->setContent( $requests->Grid('/Requests/'.$lang,'Report' ) );
        return $response;               
    }
   
    public function summaryAction()
    {
        $lang = $this->getRequest()->getLocale();

        # Appel du service
        $requests = $this->container->get('arii_core.requests');
        $Data = $requests->Summary('/Requests/'.$lang,'Report');

        return $this->render('AriiCoreBundle:Requests:summary.html.twig', array('result' => $Data ));
    }

    public function resultAction($req='',$output='html',$header=true,$footer=true)
    {
        set_time_limit(3600);        
        ini_set('memory_limit','-1');
        if ($this->container->has('profiler')) {
            $this->container->get('profiler')->disable();
        }

        $lang = $this->getRequest()->getLocale();
        $request = Request::createFromGlobals();
        if ($request->query->get( 'request' ) and $request->query->get( 'request' )!='')
            $req=$request->query->get( 'request');
        if ($request->query->get( 'output' ) and $request->query->get( 'output' )!='')
            $output=$request->query->get( 'output');

        if (!isset($req) or ($req=='')) return $this->summaryAction();        
         
        # Appel direct par request externe
        if (!strpos($req,':'))
            $req = "Report:/Requests/fr/$req";
        
        # Appel du service
        $requests = $this->container->get('arii_core.requests');
        return $requests->Display(
            $req.'.yml',
            $output,
            $this->getRequest()->query->all(),
            'Report'
        );
        
    }    
}