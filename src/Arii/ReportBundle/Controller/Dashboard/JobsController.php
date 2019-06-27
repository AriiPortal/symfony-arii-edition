<?php

namespace Arii\ReportBundle\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JobsController extends Controller
{
    public function indexAction()
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();        
        return $this->render('AriiReportBundle:Dashboard\Jobs:index.html.twig', $Filters );
    }

    // Jobs par jours
    public function dayAction()
    {       
        $Filters = $this->container->get('arii.filter')->getRequestFilter();

        // on recalcule la date temporaire
        $month = substr($Filters['monthday'],0,2);
        $day   = substr($Filters['monthday'],2);
        $start = new \DateTime($Filters['year']."-$month-$day 00:00:00");
        $end   = new \DateTime($Filters['year']."-$month-$day 23:59:59");

        $em = $this->getDoctrine()->getManager(); 
        $Jobs = $em->getRepository("AriiReportBundle:JOB")->findJobs($start,$end,$Filters['env'],$Filters['appl'],$Filters['jobClass']);
        
        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><rows>";
        $xml .= '<head><afterInit><call command="clearAll"/></afterInit></head>';        
        foreach ($Jobs as $Job) {
            $xml .= '<row id="'.$Job->getId().'">';
            $xml .= '<cell>'.$Job->getJobName().'</cell>';
            $xml .= '<cell>'.$Job->getEnv().'</cell>';
            $xml .= '<cell>'.$Job->getJobClass().'</cell>';
            $xml .= '<cell>'.$Job->getCreated()->format('Y-m-d').'</cell>';
            $xml .= '<cell>'.$Job->getUpdated()->format('Y-m-d').'</cell>';
            $xml .= '<cell>'.($Job->getFirstExecution()!=''?$Job->getFirstExecution()->format('Y-m-d'):'').'</cell>';
            $xml .= '<cell>'.($Job->getLastExecution()!=''?$Job->getLastExecution()->format('Y-m-d'):'').'</cell>';
            $xml .= '<cell>'.($Job->getDeleted()!=''?$Job->getDeleted()->format('Y-m-d'):'').'</cell>';
            $xml .= '</row>';
        }
        $xml .= '</rows>';
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;           
    }
    
    public function chartAction($mode="dashboard")
    {        
        $Filters = $this->container->get('arii.filter')->getRequestFilter();

        // on recule d'un mois pour le delta des changements
        $start = $Filters['start'];
        $start->modify('-1 month');

        $em = $this->getDoctrine()->getManager(); 
        $Jobs = $em->getRepository("AriiReportBundle:JOBMonth")->findJobsByMonth($start->format('Y')*100+$start->format('m'),$end->format('Y')*100+$end->format('m'),$env,$app);
        
        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><data>";
        $last = $n = 0;
        foreach ($Jobs as $Job) {
            if ($n>0) {
                $xml .= '<item id="'.$Job['month'].'">';
                $xml .= '<mois>'.$this->get('translator')->trans('month.'.$Job['month']).'</mois>';
                $xml .= '<jobs>'.$Job['jobs'].'</jobs>';
                $xml .= '<created>'.$Job['created'].'</created>';
                $xml .= '<deleted>'.$Job['deleted'].'</deleted>';
                $xml .= '<delta>'.($Job['jobs'] - $last).'</delta>';                
                $xml .= '</item>';
            }
            $last = $Job['jobs'];
            $n++;
        }
        $xml .= '</data>';
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;           
    }
    
    // methode par les jobs
    public function chart2Action($year=2016)
    {        
        $session = $this->container->get('arii_core.session');
        $env = $session->getEnv();
        $app = $session->getApp();
                
        $em = $this->getDoctrine()->getManager();
        if (($env == '*') and ($app=='*'))
            $Jobs = $em->getRepository("AriiReportBundle:JOB")->findAll();
        elseif ($app=='*')
            $Jobs = $em->getRepository("AriiReportBundle:JOB")->findBy(array( 'env' => $env) );
        elseif ($env=='*')
            $Jobs = $em->getRepository("AriiReportBundle:JOB")->findBy(array( 'app' => $app) );
        else 
            $Jobs = $em->getRepository("AriiReportBundle:JOB")->findBy(array( 'app' => $app, 'env' => $env) );
            
        for($i=1;$i<=12;$i++) {
            $Mois[$i]=$Created[$i]=$Deleted[$i]=0;
        }
            
        foreach ($Jobs as $Job) {
            
            $env = $Job->getEnv();
            if ($env == '')
                continue;
            
            // $created = $Job->getLastChange();
            //if (!$created) {
                $created = $Job->getCreated();
            //}
            if ($created->format('Y')==$year) {
                $mois = $created->format('m')*1;
                $Created[$mois]++;
            }

            // effacé si absent depuis 7 jour
            $deleted = $Job->getDeleted(); 
            if ($deleted) {
            }
            if ($deleted and ($deleted->format('Y')==$year)) {
                $mois = $deleted->format('m')*1;
                $Deleted[$mois]++;
            }
            
            $updated = $Job->getUpdated();
            // on boucle du mois de depart au mios de fin
            for($i=1;$i<=12;$i++) {
                // mois en cours
                $first = sprintf('%4d-%02d-%02d',$year,$i,1);
                $start = new \DateTime($first.' 00:00:00');
                $end =  new \DateTime( date("Y-m-t 23:59:59", strtotime($first)));
                
                if ($created>=$end)
                    continue;
                if ($deleted and $deleted<=$start)
                     continue;
                if ($updated <$start)
                    continue;
                
                $Mois[$i]++;
            }
        }
       
        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><data>";
        for($i=1;$i<=12;$i++) {
            $xml .= '<item id="'.$i.'">';
            $xml .= '<mois>'.$this->get('translator')->trans('month.'.$i).'</mois>';
            $xml .= '<jobs>'.$Mois[$i].'</jobs>';
            $xml .= '<created>'.$Created[$i].'</created>';
            $xml .= '<deleted>'.$Deleted[$i].'</deleted>';
            $xml .= '</item>';            
        }
        $xml .= '</data>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;            
    }

    public function gridAction($month=1,$year=2016)
    {    
        $request = Request::createFromGlobals();
        $filter = $this->container->get('arii.filter');
        $scope = $request->query->get( 'scope' );        
        if ($scope!='') {
            list($app,$env,$date) = explode(':', $scope);
            list($month,$year) = explode('-', $scope);
        }
        else {
            list($env,$app,$dayPast,$day,$month,$year,$start,$end) = $filter->getFilter();
        }
        list($env,$app,$dayPast,$day,$month,$year,$start,$end) = $filter->getFilter( 
            $env, 
            $app, 
            -32, 
            $day,
            $month, 
            $year 
        );

        $em = $this->getDoctrine()->getManager();
        if (($env == '*') and ($app=='*'))
            $Jobs = $em->getRepository("AriiReportBundle:JOB")->findAll();
        elseif ($app=='*')
            $Jobs = $em->getRepository("AriiReportBundle:JOB")->findBy(array( 'env' => $env) );
        elseif ($env=='*')
            $Jobs = $em->getRepository("AriiReportBundle:JOB")->findBy(array( 'app' => $app) );
        else 
            $Jobs = $em->getRepository("AriiReportBundle:JOB")->findBy(array( 'app' => $app, 'env' => $env) );
        
        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><rows>";
        foreach ($Jobs as $job) {
            
            $created = $job->getLastChange();
            if (!$created)
                $created = $job->getCreated();
            $status = 0;
            if ($created->format('m')==$month and $created->format('Y')==$year) {
                $status+=1;
            }
            $deleted = $job->getDeleted();
            if ($deleted and $deleted->format('m')==$month and $deleted->format('Y')==$year) {
                $status+=2;
            }
            switch ($status) {
                case '1':
                    $color='#ccebc5';
                    break;
                case '2':
                    $color='#fbb4ae';
                    break;
                case '3':
                    $color='#ffffcc';  
                    break;
            }
            if ($status>0) {
                $xml .= '<row id="'.$job->getId().'" style="background-color: '.$color.';">';
                $xml .= '<cell>'.$job->getJobName().'</cell>';
                $xml .= '<cell>'.$job->getEnv().'</cell>';
                if ($job->getCreated())
                    $xml .= '<cell>'.$job->getCreated()->format('Y-m-d').'</cell>';
                else
                    $xml .= '<cell/>';
                if ($job->getLastChange())
                    $xml .= '<cell>'.$job->getLastChange()->format('Y-m-d').'</cell>';
                else
                    $xml .= '<cell/>';
                if ($job->getFirstExecution())
                    $xml .= '<cell>'.$job->getFirstExecution()->format('Y-m-d H:i:s').'</cell>';
                else
                    $xml .= '<cell/>';
                if ($job->getLastExecution())
                    $xml .= '<cell>'.$job->getLastExecution()->format('Y-m-d H:i:s').'</cell>';
                else
                    $xml .= '<cell/>';
                if ($job->getDeleted())
                    $xml .= '<cell>'.$job->getDeleted()->format('Y-m-d').'</cell>';
                else
                    $xml .= '<cell/>';
                if ($job->getUpdated())
                    $xml .= '<cell>'.$job->getUpdated()->format('Y-m-d').'</cell>';
                else
                    $xml .= '<cell/>';
                $xml .= '</row>';
            }
        }
        $xml .= '</rows>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;            
    }

    public function exportAction($month=1,$year=2016)
    {    
        $request = Request::createFromGlobals();
        $filter = $this->container->get('arii.filter');
        $scope = $request->query->get( 'scope' );
        if ($scope!='') {
            list($app,$env,$date) = explode(':', $scope);
            list($month,$year) = explode('-', $scope);
        }
        else {
            list($env,$app,$dayPast,$day,$month,$year,$start,$end) = $filter->getFilter();
        }
$month = 2;
        list($env,$app,$dayPast,$day,$month,$year,$start,$end) = $filter->getFilter( $env, $app, -32, $month, $year );
        
        $em = $this->getDoctrine()->getManager();
        if (($env == '*') and ($app=='*'))
            $Jobs = $em->getRepository("AriiReportBundle:JOB")->findAll();
        elseif ($app=='*')
            $Jobs = $em->getRepository("AriiReportBundle:JOB")->findBy(array( 'env' => $env) );
        elseif ($env=='*')
            $Jobs = $em->getRepository("AriiReportBundle:JOB")->findBy(array( 'app' => $app) );
        else 
            $Jobs = $em->getRepository("AriiReportBundle:JOB")->findBy(array( 'app' => $app, 'env' => $env) );
        
        $csv = '';
        foreach ($Jobs as $job) {
            $created = $job->getLastChange();
            if (!$created)
                $created = $job->getCreated();
            $status = 0;
            if ($created->format('m')==$month and $created->format('Y')==$year) {
                $status+=1;
            }
            $deleted = $job->getDeleted();
            if ($deleted and $deleted->format('m')==$month and $deleted->format('Y')==$year) {
                $status+=2;
            }
            switch ($status) {
                case '1':
                    $color='#ccebc5';
                    break;
                case '2':
                    $color='#fbb4ae';
                    break;
                case '3':
                    $color='#ffffcc';
                    break;
            }
            $line = array();
                
            array_push($line,$status);
            array_push($line,$job->getJobName());
            array_push($line,$job->getEnv());
            if ($job->getCreated())
                array_push($line,$job->getCreated()->format('Y-m-d'));
            else
                array_push($line,'');            
            if ($job->getLastChange())
                array_push($line,$job->getLastChange()->format('Y-m-d'));
            else
                array_push($line,'');
            if ($job->getFirstExecution())
                array_push($line,$job->getFirstExecution()->format('Y-m-d H:i:s'));
            else
                array_push($line,'');
            if ($job->getLastExecution())
                array_push($line,$job->getLastExecution()->format('Y-m-d H:i:s'));
            else
                array_push($line,'');
            if ($job->getDeleted())
                array_push($line,$job->getDeleted()->format('Y-m-d'));
            else
                array_push($line,'');
            if ($job->getUpdated())
                array_push($line,$job->getUpdated()->format('Y-m-d'));
            else
                array_push($line,'');
            
            $csv .= implode(";",$line)."\n";            
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'text/plain');        
        $response->setContent( $csv );
        return $response;            
    }
    
    public function scatterAction($month=1,$year=2016)
    {    
        $request = Request::createFromGlobals();
        $filter = $this->container->get('arii.filter');
        list($env,$app,$dayPast,$day,$month,$year,$start,$end) = $filter->getFilter(
            $request->query->get( 'env' ),
            $request->query->get( 'app' ),
            $request->query->get( 'dayPast' ),
            $request->query->get( 'day' ),
            $request->query->get( 'month' ),
            $request->query->get( 'year' )
        );

        // on recule d'un mois pour le delta des changements
        $start->modify('-1 month');
        
        $em = $this->getDoctrine()->getManager();
        if (($env == '*') and ($app=='*'))
            $Jobs = $em->getRepository("AriiReportBundle:JOB")->findAll();
        elseif ($app=='*')
            $Jobs = $em->getRepository("AriiReportBundle:JOB")->findBy(array( 'env' => $env) );
        elseif ($env=='*')
            $Jobs = $em->getRepository("AriiReportBundle:JOB")->findBy(array( 'app' => $app) );
        else 
            $Jobs = $em->getRepository("AriiReportBundle:JOB")->findBy(array( 'app' => $app, 'env' => $env) );
        
        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><data>";
        foreach ($Jobs as $job) {
            $created = $job->getLastChange();
            if (!$created)
                $created = $job->getCreated();
            $status = 0;
            if ($created->format('m')==$month and $created->format('Y')==$year) {
                $status+=1;
            }
            $deleted = $job->getDeleted();
            if ($deleted and $deleted->format('m')==$month and $deleted->format('Y')==$year) {
                $status+=2;
            }
            
            if ($status==0)
                continue;
            
            switch ($status) {
                case '1':
                    $date = $job->getCreated();
                    $color='green';
                    break;
                case '2':
                    $date = $job->getDeleted();
                    $color='red';
                    break;
                case '3':
                    $date = $job->getCreated();
                    $color='orange';
                    break;
            }
            $xml .= '<item id="'.$job->getId().'">';
            $xml .= '<day>'.$date->format('d').'</day>';
            $xml .= '<hour>'.$date->format('H').'</hour>';         
            $xml .= '<color>'.$color.'</color>';         
            $xml .= '</item>';            
        }
        $xml .= '</data>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;            
    }
    

    public function Applications_MonthAction($app='',$env='',$mode='dashboard')
    {
        $request = Request::createFromGlobals();
        $filter = $this->container->get('arii.filter');
        list($env,$app,$dayPast,$day,$month,$year,$start,$end) = $filter->getFilter(
            $request->query->get( 'env' ),
            $request->query->get( 'app' ),
            -120,
            $request->query->get( 'day' ),
            $request->query->get( 'month' ),
            $request->query->get( 'year' )
        );
        
        $em = $this->getDoctrine()->getManager();
        $Jobs = $em->getRepository("AriiReportBundle:JOBMonth")->findApplicationsByMonths($start->format('Y')*100+$start->format('m'),$end->format('Y')*100+$end->format('m'),$env,$app);
        
        // Tableau des mois 
        $date = clone $start;
        $interval = new \DateInterval("P1M");
        $n=0;
        while ($date <= $end) {            
            $month = $date->format('m');
            $id = $date->format('Y').$date->format('m');
            $Mois[$id] = [           
                'order' => $n,
                'month' => $this->get('translator')->trans('month.'.(substr('0'.$month,-2)*1))
            ];
            $date->add($interval);
            $n++;
        }

        // on reorganise par mois
        $Chart = array();
        foreach ($Jobs as $job) {
            $app = $job['application'];
            $date = sprintf("%04d%02d",$job['year'],$job['month']);
            $order = $Mois[$date]['order'];
            $Chart[$app]['application'] = $app;
            $Chart[$app]['month'.$order] = $job['jobs'];
            $Chart[$app]['month'.$order.'_str'] = $Mois[$date]['month'];
        }

        $portal = $this->container->get('arii_core.portal');
        $App = $portal->getApplications();
        
        // on calcul l'évolution
        // attention aux nouvelles applications et aux décommissionnées
        $limit=0;
        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><data>";
        foreach ($Chart as $id=>$chart) {            
            $row = '<item id="'.$id.'">';
            $a = $chart['application'];
            if (isset($App[$a]))                
                $row .= '<application>'.$App[$a]['title'].'</application>';
            else 
                $row .= '<application>['.$a.']</application>';
            $total = 0;
            if (isset($chart['month0']))
                $last = $chart['month0'];
            else 
                $last = 0; // nouvelle application
            $delta=0;
            for($i=1;$i<$n;$i++) { 
                if (isset($chart['month'.$i])) 
                    $nb = $chart['month'.$i];
                else
                    $nb = 0;
                $evolution = $nb - $last;
                $row .= '<month'.$i.'>'.$evolution.'</month'.$i.'>';
                $delta += $evolution;
                $last = $nb;
            }
            $row .= '</item>';
            // si  aucune evolution, on supprime la ligne
            $limit++;            
            if ($delta>0)
                $xml .= $row;
        }
        $xml .= '</data>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;            
    }
    
    public function creationChartAction()
    {
        $request = Request::createFromGlobals();
        $filter = $this->container->get('arii.filter');
        list($env,$application,$dayPast,$day,$month,$year,$start,$end) = $filter->getFilter(
            $request->query->get( 'env' ),
            $request->query->get( 'app' ),
            $request->query->get( 'dayPast' ),
            $request->query->get( 'day' ),
            $request->query->get( 'month' ),
            $request->query->get( 'year' )
        );

        $em = $this->getDoctrine()->getManager();
        $Runs = $em->getRepository("AriiReportBundle:JOB")->findCreationByMonth($start);
        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><data>";
        foreach ($Runs as $run) {
            if ($run['nb']>0) {
                $xml .= '<item id="'.$run['application'].'">';
                $xml .= '<mois>'.$run['mois'].'</mois>';
                $xml .= '<application>'.$run['application'].'</application>';
                $xml .= '<jobs>'.$run['nb'].'</jobs>';
                $xml .= '</item>';
            }
        }
        $xml .= '</data>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;            
    }
    
}

