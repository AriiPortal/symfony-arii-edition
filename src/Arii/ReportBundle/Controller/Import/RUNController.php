<?php

namespace Arii\ReportBundle\Controller\Import;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RUNController extends Controller
{
 
    public function __construct() {
    }
    
    // Mise a jour des jobs
    // on peut les faire par cycle !
    // Utile seulement pour un rattrapage
    public function JOBAction()            
    {
        $debut = time();
        if ($this->container->has('profiler')) {
            $this->container->get('profiler')->disable();
        }             
        set_time_limit(3600);
        ini_set('memory_limit','-1');
        $em = $this->getDoctrine()->getManager();
        $Runs = $em->getRepository("AriiReportBundle:RUN")->findMinMaxDates();
        $new = $upd = $no = 0;
        $n = 0;
        foreach ($Runs as $run) {
            $job_name = $run['job_name'];
            
            $first_execution= $run['first_execution'];
            $last_execution= $run['last_execution'];
            $spooler_name = $run['spooler_name'];      
            
            $Job = $em->getRepository("AriiReportBundle:JOB")->findOneBy(
                    array(
                        'job_name'=> $job_name, 
                        'spooler_name'=> $spooler_name ));

            $update = false;
            $first = new \Datetime($first_execution);
            if ($Job and (!$Job->getFirstExecution() or ($first<$Job->getFirstExecution()))) {
                $Job->setFirstExecution($first);
                $update = true;
            }
            $last = new \Datetime($last_execution);
            if ($Job and (!$Job->getLastExecution() or ($last<$Job->getLastExecution()))) {
                $Job->setLastExecution($last);
                $update = true;
            }
            if ($update) {
                $em->persist($Job);
                $n++;
                if ($n % 100==0) {
                    print ".";
                    $em->flush();
                }
            }
        }
        $em->flush();
        return new Response(time()-$debut);
    }

    // Fonction primordiale car les aures se basent dessus
    public function RunHourAction($force=1,$html=0,$days=10)
    {
        set_time_limit(7200);
        ini_set('memory_limit', '-1');        
        if ($this->container->has('profiler'))
            $this->container->get('profiler')->disable();

        // par défaut
        $request = Request::createFromGlobals();
        // Mode automatique, reprise sur 10 jours
        if ($request->query->get( 'days' )!='') {
            $days = $request->query->get( 'days' );
        }
        
        // forcer la date pour les reprises
        if ($request->query->get( 'date' )!='') {
            $start = new \DateTime($request->query->get( 'date' ));
            $end = clone $start;
            $end->modify($days.' days');
        }
        // mode auto
        elseif ($request->query->get( 'day' )=='') {
            $end = new \DateTime();
            $start = clone $end;
            // attention au rattrappage  de ticket
            $start->modify('-'.$days.' days');
        }
        // a travers l'interface
        else {
            $Filters = $this->container->get('arii.filter')->getRequestFilter();
            $end = $Filters['end'];
            $start = $Filters['start'];
        }
        if ($request->query->get( 'force' )!='')
            $force = $request->query->get( 'force' );
        if ($request->query->get( 'html' )!='')
            $html = $request->query->get( 'html' );
                        
        $em = $this->getDoctrine()->getManager();       
        
        $Runs = $em->getRepository("AriiReportBundle:RUN")->findRunHours($start,$end);
        $Run = array();
        $Done = array(); // Ticket déja traité
        foreach ($Runs as $run) {   
            // supression des status inutiles
            if ($run['status']=='INACTIVE') continue;
                
            $app    = $run['app'];
            $env    = $run['env'];
            $jobClass  = $run['jobClass'];
            $spooler_name  = $run['spooler_name'];
            
            $date = substr(str_replace('-','',$run['start_date']),0,8);
            $hour = $run['start_hour'];
            $id = "$spooler_name:$app:$env:$jobClass:$date:$hour";

            if (!isset($Run[$id])) {
                $Run[$id]['executions'] = 0;
                $Run[$id]['acks'] = 0;
                $Run[$id]['warnings'] = 0;            
                $Run[$id]['alarms'] = 0;             
                $Run[$id]['date'] = $date;
                $Run[$id]['hour'] = $hour;
                $Run[$id]['warnings'] = 0;
            }

            $Run[$id]['app'] = $run['app'];
            $Run[$id]['spooler_name'] = $run['spooler_name'];
            $Run[$id]['env'] = $run['env'];
            $Run[$id]['jobClass'] = $run['jobClass'];
            
            switch ($run['status']) {
                case 'SUCCESS':
                case 'FAILURE':
                case 'TERMINATED':
                    $Run[$id]['executions'] += $run['executions'];
                    break;
                case 'MINRUNALARM':
                case 'MAXRUNALARM':    
                case 'RESTART':
                    $Run[$id]['warnings'] += $run['alarms'];
                    break;
                
            }
            // Chiffre grossier a affiner pour supprimer les doublons
            $Run[$id]['acks'] += $run['acks'];
            $Run[$id]['alarms'] += $run['alarms'];            
        }

        $nb=$upd=$new=0;
        foreach ($Run as $run) {
            $date = new \Datetime($run['date']);
            $hour = $run['hour'];
            $Agg = $em->getRepository("AriiReportBundle:RUNHour")->findOneBy(
                array(  'spooler_name' => $run['spooler_name'],
                        'app'  => $run['app'], 
                        'env' => $run['env'], 
                        'jobClass' => $run['jobClass'], 
                        'date' => $date, 
                        'hour' => $hour ));
            if (!$Agg) {
                $Agg = new \Arii\ReportBundle\Entity\RUNHour();
                if ($html>0) {
                    print $run['spooler_name']." ".$run['app']." ".$run['env']." ".$run['jobClass']." ".$run['date']." ".$run['hour']."<br/>" ;
                }
                $new++;
            }
            else {
                if (!$force) continue;
                $upd++;
            }
            $Agg->setDate($date);
            $Agg->setHour($hour);
            $Agg->setEnv($run['env']);
            $Agg->setJobClass($run['jobClass']);      
            $Agg->setApp($run['app']);

            if ($run['executions']=='') $run['executions']=0;
            $Agg->setExecutions($run['executions']);
            if ($run['alarms']=='') $run['alarms']=0;
            $Agg->setAlarms($run['alarms']);
            if ($run['warnings']=='') $run['warnings']=0;
            $Agg->setWarnings($run['warnings']);
            if ($run['acks']=='') $run['acks']=0;
            $Agg->setAcks($run['acks']);
            $Agg->setSpoolerName($run['spooler_name']);
            $Agg->setUpdated(new \DateTime());

            $em->persist($Agg);
            if ($nb % 1000 == 0) {
                print ".";
                $em->flush();
            }
            $nb++;
        }        
        $em->flush();
        return new Response("From ".$start->format('Y-m-d H:i:s')." to ".$end->format('Y-m-d H:i:s').": Runs/Hour New ($new) Update ($upd)");
    }

    // Ajout des issues sur la table RUNHour
    // On ne compte que les tickets
    // UPDATE GLOBAL
    public function IssuesAction()
    {
        set_time_limit(3600);
        ini_set('memory_limit', '-1');        
        if ($this->container->has('profiler'))
            $this->container->get('profiler')->disable();

        // par défaut
        // Rattrapage sur un mois
        $request = Request::createFromGlobals();
        $em = $this->getDoctrine()->getManager();       

        $Runs = $em->getRepository("AriiReportBundle:RUN")->findIssues();

        $Issues = array();
        // Creation d'un tableau des valeurs
        foreach ($Runs as $run) {
            $date = substr($run['date'],0,10);
            $hour = substr($run['date'],11,2);
            $id = implode(':', [ 
                $run['spooler_name'], 
                $run['app'],
                $run['env'],
                $run['jobClass'],
                $date,
                $hour ] );
            
            if (isset($Issues[$id]))
                $Issues[$id]++;
            else
                $Issues[$id]=1;                    
        } 

        // On reprend tous les enregistrements 
        // qui ont des issues pour corrections
        $upd=$del=0;
        $Runs = $em->getRepository("AriiReportBundle:RUNHour")->findIssues();

        foreach ($Runs as $run) {
            $date = $run->getDate()->format('Y-m-d');
            $hour = $run->getHour();
            $spooler_name = $run->getSpoolerName();
            $app = $run->getApp();
            $env = $run->getEnv();
            $jobClass = $run->getJobClass();
            
            $id = implode(':', [ 
                $spooler_name, 
                $app,
                $env,
                $jobClass,
                $date,
                $hour ] );
            
            if (isset($Issues[$id])) {
                $new = $Issues[$id];
                $Issues[$id]=0;
            }
            else {
                $new = null;
                $del++;
            }
            
            $old =  $run->getIssues();
            if ($new<>$old) {
                $run->setIssues($new);
                $em->persist($run);
                $upd++;
            }
        }
        $em->flush();
        
        // On reprends les nouveaux
        $new = 0;
        foreach ($Issues as $k=>$v) {
            if ($v==0) continue;
            list( $spooler_name, $app, $env, $jobClass,$d,$hour ) = explode(':',$k);
            $date = new \DateTime($d);
            $run = $em->getRepository("AriiReportBundle:RUNHour")->findOneBy([
                        'spooler_name' => $spooler_name, 
                        'app' => $app, 
                        'env' => $env, 
                        'jobClass' => $jobClass,
                        'date' => $date,
                        'hour' => $hour
                    ]);
            if (!$run) { # Ticket créé après l'incident (entre 6h et 22h) {
                $run = new \Arii\ReportBundle\Entity\RUNHour;
                $run->setDate($date);
                $run->setHour($hour);
                $run->setSpoolerName($spooler_name);
                $run->setApp($app);
                $run->setEnv($env);
                $run->setJobClass($jobClass);                
                $run->setExecutions(0);                
            }
            
            if ($v==0) $v=null;
            $run->setIssues($v);
            $em->persist($run);
            $new++;
        }
        $em->flush();
        return new Response("new ($new) upd ($upd) del ($del)");
    }
    
    // Regroupement des heures en jours
    public function RunDayAction($force=1,$html=0,$days=10)
    {
        set_time_limit(3600);
        ini_set('memory_limit', '-1');        
        if ($this->container->has('profiler'))
            $this->container->get('profiler')->disable();

        // par défaut
        $request = Request::createFromGlobals();
        // Mode automatique, reprise sur 10 jours
        if ($request->query->get( 'days' )!='') {
            $days = $request->query->get( 'days' );
        }
        
        // forcer la date pour les reprises
        if ($request->query->get( 'date' )!='') {
            $start = new \DateTime($request->query->get( 'date' ));
            $end = clone $start;
            $end->modify($days.' days');
        }
        // mode auto
        elseif ($request->query->get( 'day' )=='') {
            $end = new \DateTime();
            $start = clone $end;
            // attention au rattrappage  de ticket
            $start->modify('-'.$days.' days');
        }
        // a travers l'interface
        else {
            $Filters = $this->container->get('arii.filter')->getRequestFilter();
            $end = $Filters['end'];
            $start = $Filters['start'];
        }
        if ($request->query->get( 'force' )!='')
            $force = $request->query->get( 'force' );
        if ($request->query->get( 'html' )!='')
            $html = $request->query->get( 'html' );
         
        $em = $this->getDoctrine()->getManager();       
        
        $Runs = $em->getRepository("AriiReportBundle:RUNHour")->findRunsByDay($start,$end);

        $Run = array();
        $upd=$new=0;
        foreach ($Runs as $run) {
            $Agg = $em->getRepository("AriiReportBundle:RUNDay")->findOneBy(
                array(  'date' => $run['date'], 
                        'app'=> $run['app'] , 
                        'env' => $run['env'], 
                        'jobClass' => $run['jobClass'], 
                        'spooler_name' => $run['spooler_name'] ) 
            );
            if (!$Agg) {
                $Agg = new \Arii\ReportBundle\Entity\RUNDay();
                $new++;
            }
            else {
                $upd++;
            }            
            $Agg->setDate($run['date']);
            $Agg->setEnv($run['env']);
            $Agg->setApp($run['app']);
            $Agg->setSpoolerName($run['spooler_name']);
            $Agg->setJobClass($run['jobClass']);
            
            // doit etre inutile.
            if ($run['executions']=='') $run['executions']=0;
            $Agg->setExecutions($run['executions']);
            if ($run['alarms']=='') $run['alarms']=0;
            $Agg->setAlarms($run['alarms']);
            if ($run['warnings']=='') $run['warnings']=0;
            $Agg->setWarnings($run['warnings']);
            if ($run['acks']=='') $run['acks']=0;
            $Agg->setAcks($run['acks']);
            
            $em->persist($Agg);
        }        
        $em->flush();
        return new Response("From ".$start->format('Y-m-d H:i:s')." to ".$end->format('Y-m-d H:i:s').": Runs/Day New ($new) Update ($upd)");
    }
    
    /* Agrégation des runs par mois */
    public function RunMonthAction()
    {       
        set_time_limit(3600);
        ini_set('memory_limit','-1');
        if ($this->container->has('profiler')) {
            $this->container->get('profiler')->disable();
        }             

        // par défaut
        $request = Request::createFromGlobals();
        if ($request->query->get( 'day' )=='') {
            $end = new \DateTime();
            $start = clone $end;
            $start->modify('-10 days');
        }
        else {
            $Filters = $this->container->get('arii.filter')->getRequestFilter();
            $end = $Filters['end'];
            $start = $Filters['start'];            
        }
        if ($request->query->get( 'force' )!='')
            $force = $request->query->get( 'force' );
        if ($request->query->get( 'html' )!='')
            $html = $request->query->get( 'html' );
 
        $debut = time();
        
        $em = $this->getDoctrine()->getManager();
        // Il faut reparser tous les jobs
        // limiter par spooler ? 
        $Runs = $em->getRepository("AriiReportBundle:RUNDay")->findRunsByMonth($start,$end);
        $n = $new = $upd = 0;
        foreach ($Runs as $Run) {
            $app = $Run['app'];
            $env = $Run['env'];
            $jobClass = $Run['jobClass'];
            $month = $Run['run_month'];
            $year = $Run['run_year'];
            $spooler_name = $Run['spooler_name'];
            $runs = $Run['runs'];
            $warnings = $Run['warnings'];
            $alarms = $Run['alarms'];
            $acks = $Run['acks'];
            
            $RunMonth = $em->getRepository("AriiReportBundle:RUNMonth")->findOneBy(
                array(  'app'=>$app,
                        'env' =>$env, 
                        'spooler_name' => $spooler_name, 
                        'month' => $month, 
                        'year' => $year
                )
            );

            if (!$RunMonth) {
                $RunMonth = new \Arii\ReportBundle\Entity\RUNMonth;
                $new++;
            }
            else {
                $upd++;
            }
            
            $RunMonth->setApp($app);            
            $RunMonth->setEnv($env);
            $RunMonth->setJobClass($jobClass);
            $RunMonth->setMonth($month);
            $RunMonth->setYear($year);
            
            $RunMonth->setSpoolerName($spooler_name);
            
            $RunMonth->setExecutions($runs);
            $RunMonth->setWarnings($warnings);
            $RunMonth->setAlarms($alarms);
            $RunMonth->setAcks($acks);
            
            $em->persist($RunMonth);
            $n++;
        }        
        $em->flush();        

        return new Response("From ".$start->format('Y-m-d H:i:s')." to ".$end->format('Y-m-d H:i:s').": Runs/Month New ($new) Update ($upd)");
    }
}

