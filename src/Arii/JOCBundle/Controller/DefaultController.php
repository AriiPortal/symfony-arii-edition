<?php

namespace Arii\JOCBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Yaml\Parser;

class DefaultController extends Controller
{
    protected $images;
    protected $TZLocal;
    protected $TZSpooler;
    protected $TZOffset;
    protected $CurrentDate;
    
    public function __construct( )
    {
          $request = Request::createFromGlobals();
          $this->images = $request->getUriForPath('/../arii/images/wa');
          
          $this->CurrentDate = date('Y-m-d');
    }

    public function indexAction()   
    {
        return $this->render('AriiJOCBundle:Default:index.html.twig' );
    }

    public function readmeAction()
    {
        return $this->render('AriiJOCBundle:Default:readme.html.twig');
    }

    public function swaggerAction()
    {
        $portal = $this->container->get('arii_core.portal');
        return $this->render('AriiJIDBundle:Default:swagger.html.twig', [ 'db' => $portal->getDatabase() ]);
    }
        
    public function menuAction() {
        
        // Etat actuel des spoolers
        $Requests = $this->container->get('arii_core.requests');
        $JOC = $Requests->readStatus( 'JOC' );

        $spooler_paused = $spooler_lost = 0;
        if (isset($JOC['JOC:SPOOLERS PAUSED']['results']) and ($JOC['JOC:SPOOLERS PAUSED']['results']>0))
            $spooler_paused = $JOC['JOC:SPOOLERS PAUSED']['results'];
        if (isset($JOC['JOC:SPOOLERS LOST']['results']) and ($JOC['JOC:SPOOLERS LOST']['results']>0))
            $spooler_lost = $JOC['JOC:SPOOLERS LOST']['results'];

        $order_suspended = 0;
        if (isset($JOC['JOC:SUSPENDED']['results']) and ($JOC['JOC:SUSPENDED']['results']>0))
            $order_suspended = $JOC['JOC:SUSPENDED']['results'];        

        $chain_stopped = 0;
        if (isset($JOC['JOC:CHAINS STOPPED']['results']) and ($JOC['JOC:STOPPED']['results']>0))
            $chain_stopped = $JOC['JOC:CHAINS STOPPED']['results'];        
        
        $job_stopped = 0;
        if (isset($JOC['JOC:JOBS STOPPED']['results']) and ($JOC['JOC:JOBS STOPPED']['results']>0))
            $job_stopped = $JOC['JOC:JOBS STOPPED']['results'];        
        
        $lock_used = $lock_missing = 0;
        if (isset($JOC['JOC:LOCKS NOT AVAILABLE']['results']) and ($JOC['JOC:LOCKS NOT AVAILABLE']['results']>0))
            $lock_used = $JOC['JOC:LOCKS NOT AVAILABLE']['results'];
        if (isset($JOC['JOC:LOCKS_MISSING']['results']) and ($JOC['JOC:LOCKS_MISSING']['results']>0))
            $lock_missing = $JOC['JOC:LOCKS_MISSING']['results'];

        $response = new Response();        
        $response->headers->set('Content-Type', 'text/xml');
        $list = '<?xml version="1.0" encoding="UTF-8"?>';
        $list .= '<data total_count="2" pos="0">
           <item id="0">
                <name>Spoolers</name>
                <img>spooler</img>
                <warning>'.$spooler_paused.'</warning>
                <error>'.$spooler_lost.'</error>
            </item>
            <item id="1">
                <name>Chains</name>
                <img>job_chain</img>
                <error>'.$chain_stopped.'</error>
            </item>
            <item id="2">
                <name>Orders</name>
                <img>order</img>
                <error>'.$order_suspended.'</error>
            </item>
            <item id="3">
                <name>Jobs</name>
                <img>job</img>
                <error>'.$job_stopped.'</error>
            </item>
            <item id="4">
                <name>Process classes</name>
                <img>process_class</img>
            </item>
            <item id="5">
                <name>Locks</name>
                <img>lock</img>
                <warning>'.$lock_used.'</warning>
                <error>'.$lock_missing.'</error>
            </item>
        </data>';
        $response->setContent( $list );
        return $response;               
    }

    
    public function plannedAction()
    {
        return $this->render('AriiJOCBundle:Default:planned.html.twig', 
                array(  'refresh'=>$this->getRefresh() )
                );
    }
    
    public function planned_pieAction()
    {
        return $this->render('AriiJOCBundle:Default:planned_pie.html.twig' );
    }

    public function historyAction()
    {
        return $this->render('AriiJOCBundle:Default:history.html.twig' );
    }

    public function history_pieAction()
    {
        return $this->render('AriiJOCBundle:Default:history_pie.html.twig' );
    }

    public function history_timelineAction()
    {
        return $this->render('AriiJOCBundle:Default:history_timeline.html.twig');
    }

    public function messagesAction()
    {
        return $this->render('AriiJOCBundle:Default:messages.html.twig');
    }
    
    public function spoolersAction()
    {
        return $this->render('AriiJOCBundle:Default:spoolers.html.twig');
    }

    public function timelineAction()
    {
        return $this->render('AriiJOCBundle:Default:timeline.html.twig');
    }

    public function pie_chartAction()
    {
        $request = $here = $this->getRequest()->getPathInfo();
        if (strpos($request,"/orders"))
            return $this->render('AriiJOCBundle:Sidebar:pie_chart_orders.html.twig');
        return $this->render('AriiJOCBundle:Sidebar:pie_chart.html.twig');
    }

    public function job_infoAction()
    {
        $request = $here = $this->getRequest()->getPathInfo();
        if (strpos($request,"/orders"))
            return $this->render('AriiJOCBundle:Sidebar:job_info_orders.html.twig');
        return $this->render('AriiJOCBundle:Sidebar:job_info.html.twig');
    }
    
        
}
