<?php

namespace Arii\ReportBundle\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{
    
    public function indexAction()
    {                
        if ($this->container->has('profiler')) {
            $this->container->get('profiler')->disable();
        }    
        
        // on peut forcer la session pour le mode batch        
        $request = Request::createFromGlobals();
        // format 
        $header = 1;
        if ($request->query->get('header')!='') 
            $header = $request->query->get('header');
        
        $filter = $this->container->get('report.filter');
        list($env,$app,$dayPast,$day,$month,$year,$start,$end) = $filter->getFilter(
            $request->query->get('env'),
            $request->query->get('app'),
            $request->query->get('dayPast'),
            $request->query->get('day' ),                
            $request->query->get('month'),
            $request->query->get('year')
        );

        $month_str = $this->get('translator')->trans('month.'.$month);
        
        if ($env!='*') {
            $environment = $this->get('translator')->trans('env.'.$env);
        }
        else {
            $environment = $this->get('translator')->trans('All environments');
        }

        // Applications
        $portal = $this->container->get('arii_core.portal');
        $Apps = $portal->getApplications();
                
        // rechercher l'application
        if ($app != '*') {           
            if (!isset($Apps[$app])) {
                $response = new Response();
                $response->setContent( "Application $app ?!" );
                return $response;                        
            }
            $application = $Apps[$app]['title'];
        }
        else {
            $application = $this->get('translator')->trans('All applications');
        }
        
        // On prepare les informations sur les deux derniers mois
        // Nombre de jobs et delta
        $delta = clone $end;
        $delta->modify('-2 month');
        $delta->modify('first day of this month');
        
        // Jobs
        $em = $this->getDoctrine()->getManager(); 
        $Jobs = $em->getRepository("AriiReportBundle:JOBMonth")->findJobsByMonth($delta->format('Y')*100+$delta->format('m'),$end->format('Y')*100+$end->format('m'),$env,$app);      
        $last_jobs = 0;
        $jobs = $jobs_delta = $jobs_max = 0;
        foreach ($Jobs as $Job) {
            $jobs = $Job['jobs'];
            $jobs_delta = $jobs - $last_jobs;                
            $last_jobs = $jobs;
        }

        // Executions et alarmes
        $Runs = $em->getRepository("AriiReportBundle:RUNMonth")->findExecutionsByMonth($delta->format('Y')*100+$delta->format('m'),$end->format('Y')*100+$end->format('m'),$env,$app);
        $last_runs = $last_alarms = $last_acks = 0;
        $runs = $runs_delta = $run_max = $alarms = $alarms_delta = $acks = $acks_delta = 0;
        foreach ($Runs as $Run) {
            $runs = $Run['executions'];
            $runs_delta = $runs - $last_runs;                
            $last_runs = $runs;
            
            $alarms = $Run['alarms'];
            $alarms_delta = $alarms - $last_alarms;                
            $last_alarms = $alarms;
            
            $acks = $Run['acks'];
            $acks_delta = $acks - $last_acks;                
            $last_acks = $acks;
        }
        
        // Pour ce mois
        $last = clone $end;
        $last->modify('first day of this month');        
        
        // Merge des requetes ?
        $Jobs = $em->getRepository("AriiReportBundle:JOBMonth")->findAppsByMonths($end->format('Y')*100+$end->format('m'),$end->format('Y')*100+$end->format('m'),$env);
        $JobApps=[];
        foreach ($Jobs as $Job) {
            $a = $Job['app'];
            if ($Job['jobs']>=$jobs_max)
            $JobApps[$a]=$Job['jobs'];
        }
        arsort($JobApps);
     
        $Runs = $em->getRepository("AriiReportBundle:RUNMonth")->findAppsByMonths($end->format('Y')*100+$end->format('m'),$end->format('Y')*100+$end->format('m'),$env);
        $RunApps=[];
        foreach ($Runs as $Run) {
            $a = $Run['app'];
            $RunApps[$a]=$Run['executions'];
        }
        arsort($RunApps);

        // en l'absence de SLA, la note est la proportion de Jira traité par alarme
        //if ($alarms>0)
        //    $note = round(($alarms-$acks)*4/$alarms);
        // nombre d'acquittement par jour ?
        $note = round($acks/15);
        
        if ($note>4) $note=4;

        // Pour les tests
        $AllApps = [];
                
        // Indications en texte
        $Applications = [];
        $n = 0;
        foreach ($JobApps as $job=>$v) {
            if ($n>=10) continue;
            if (isset($Apps[$job]) and ($Apps[$job]['active']>0)) {
                array_push($Applications,$Apps[$job]);
                $title=strtolower($Apps[$job]['title']);
                $AllApps[$title] = $Apps[$job];
                $n++;
            }
        }
 
        $Executions = [];
        $n = 0;
        foreach ($RunApps as $run=>$v) {
            if ($n>=10) continue; 
            if (!isset($Apps[$run])) continue;
            if (isset($Apps[$run]) and ($Apps[$run]['active']>0)) {
                array_push($Executions,$Apps[$run]);
                $title=strtolower($Apps[$run]['title']);
                $AllApps[$title] = $Apps[$run];
                $n++; 
            }
        }
        
        // Alertes
        
        ksort($AllApps);

        // Evenements du mois en cours
        $Issues = [];        
        $Events = $em->getRepository("AriiCoreBundle:Event")->findEvents($last,$end);        
        foreach ($Events as $Event) {
            $App = $portal->getApplicationById($Event['application_id']);
            if (empty($App)) 
                $impact = "GLOBAL";
            else
                $impact = $App['title'];
            // pour le tri
            $id = $Event['start_time']->format('d').$impact;
            $Issues[$id] = [
                'name' => $Event['name'],
                'title' => $Event['title'],
                'start_time' => $Event['start_time'],
                'event_type' => $Event['event_type'],
                'description' => str_replace("\n",'<br/>',$Event['description']),
                'impact' => $impact
            ];
        }
        ksort($Issues); 
    
        return $this->render('AriiReportBundle:Dashboard:bootstrap.html.twig', 
            array(  'month' => $month,
                    'month_str' => $month_str,
                    'year'  => $year,    
                    'dayPast' => $dayPast,
                    'app'   => $app,
                    'application' => $application,
                    'env' => $env,
                    'environment' => $environment,
                    'jobs'  => $jobs,
                    'jobs_delta' => $jobs_delta,
                    'runs' => $runs,
                    'runs_delta' => $runs_delta,
                    'alarms' => $alarms,
                    'alarms_delta' => $alarms_delta,
                    'acks' => $acks,
                    'acks_delta' => $acks_delta,
                    'note' => $note,
                    'header' => $header,
                    'JobApps' => $Applications,
                    'RunApps' => $Executions,
                    'Events' => $Issues,
                    'AllApps' => $AllApps
                )
        );
    }
    
    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        return $this->render('AriiReportBundle:Dashboard:toolbar.xml.twig',array(), $response );
    }
    
}

