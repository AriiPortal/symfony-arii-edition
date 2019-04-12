<?php

namespace Arii\ReportBundle\Controller\Import;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class JOBController extends Controller
{

    /* Nettoyage des donnees */
    public function cleanAction($limit=10)
    {       
        set_time_limit(3600);        
        ini_set('memory_limit','-1');
        if ($this->container->has('profiler')) {
            $this->container->get('profiler')->disable();
        }             

        $debut = time();
        
        $date_limit = new \DateTime();
        $date_limit->sub(new \DateInterval(sprintf("P%dD",$limit)));

        $em = $this->getDoctrine()->getManager();
        // Il faut reparser tous les jobs
        // limiter par spooler ? 
        $Jobs = $em->getRepository("AriiReportBundle:JOB")->findAll();
        $upd_job = $del_job = 0;
        foreach ($Jobs as $Job) {           
            $update = 0;
            
            $created =  $Job->getCreated();
            $first_execution = $Job->getFirstExecution();
            
            // correction
            // La date de creation est au moins la date de derniere execution
            if ($first_execution and ($first_execution<$created)) {
                $Job->setCreated($first_execution);
                $start = $first_execution;
                $update++;
            }
            else {
                $start = $created;
            }
            
            // Si un job a une nouvelle date d'execution
            // c'est qu'il n'est pas supprimé
            $deleted =  $Job->getDeleted();
            $last_execution = $Job->getLastExecution();
            $end = new \DateTime(); 
            if ($deleted) {
                if ($last_execution and ($last_execution>$deleted)) {
                    // il n'est finalement pas supprimé
                    $Job->setDeleted(null);
                    $update++;
                }
                else {
                    $end = $deleted; 
                }
            }
            
            // Reprise 
            // Derniere mise à jour
/*            
            $maxdate = clone $Job->getCreated();
            if ($Job->getLastChange()>$maxdate)
                $maxdate = $Job->getLastChange();
            if ($Job->getFirstExecution()>$maxdate)
                $maxdate = $Job->getFirstExecution();
            if ($Job->getLastExecution()>$maxdate)
                $maxdate = $Job->getLastExecution();
            if ($maxdate > $Job->getUpdated()) {
                $Job->setUpdated($maxdate);
                $update++;
            }
            
            // Suppression
            if ($Job->getUpdated()<$date_limit) {
                $Job->setDeleted(new \DateTime());
                $update++;
            }                  
*/
            // On ajoute ce job sur tous les mois concernés
            if ($update>0) {
                $upd_job++;
                $em->persist($Job);                
            }
/*            elseif ($Job->getUpdated()<$date_limit) {
                $del_job++;
                $Job->setDeleted(new \DateTime());
                $em->persist($Job);            
            }
*/
        }       
        $em->flush();        
        return new Response("Job clean ($upd_job) (-$del_job) [".(time()-$debut)."]");
    }
 
    /* Agrégation des jobs par applications et par jours */
    public function JobDayAction()
    {       
        set_time_limit(3600);        
        ini_set('memory_limit','-1');
        if ($this->container->has('profiler')) {
            $this->container->get('profiler')->disable();
        }             

        $debut = time();

        $em = $this->getDoctrine()->getManager();
        // Il faut reparser tous les jobs
        // limiter par spooler ? 
        // $Jobs = $em->getRepository("AriiReportBundle:JOB")->findDates();
        
        $Jobs = $em->getRepository("AriiReportBundle:JOB")->findDates();
        
        $upd_job = 0;
        $nb=0;

        foreach ($Jobs as $Job) {
            // Preparation des données
            $app       = $Job['app'];
            $env       = $Job['env'];
            $jobClass = $Job['jobClass'];            
            $spooler   = $Job['spooler_name'];            
            
            $created = new \Datetime($Job['created']);
            $deleted = new \Datetime($Job['deleted']);
            $updated = new \Datetime($Job['updated']);
            $end = ($Job['deleted']!=''?$deleted:$updated);
            
            $id = "$app#$env#$jobClass#$spooler";
            
            // on ajoute directement le created et le deleted
            $id_created = $id.'#'.$created->format("Y-m-d");            
            if (!isset($Created[$id_created]))
                $Created[$id_created] = 0;
            $Created[$id_created] += $Job['jobs'];

            // si on est dans le delete
            if ($Job['deleted']!='') {
                $id_deleted = $id.'#'.$deleted->format("Y-m-d");            
                if (!isset($Deleted[$id_deleted]))
                    $Deleted[$id_deleted] = 0;
                $Deleted[$id_deleted] += $Job['jobs'];
            }
            
            // la fin est la derniere mise a jour
            $interval = \DateInterval::createFromDateString('1 day');
            $end->add($interval);            
            $period = new \DatePeriod($created, $interval, $end);
            
            // On remplit chaque jour pour toute la période
            foreach ( $period as $dt ) {
                $date = $dt->format( "Y-m-d" );
                $id_date = $id.'#'.$date;
                if (!isset($Exist[$id_date]))
                    $Exist[$id_date]=0;
                $Exist[$id_date] += $Job['jobs'];
            }
        }
        
        // Mise en base de donnees
        $new=0;
        
        // Truncate        
        $connection = $em->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $q = $dbPlatform->getTruncateTableSql('REPORT_JOB_DAY');
        $connection->executeUpdate($q);
        
        foreach ($Exist as $k=>$jobs) {            
            list($app,$env,$jobClass,$spooler_name,$date) = explode('#',$k);
            
            // truncate ?! si on truncate on perd l'historique
/*            
            $JobDay = $em->getRepository("AriiReportBundle:JOBDay")->findOneBy(
                array( 
                    'app'=>$app,
                    'env' =>$env,
                    'jobClass' =>$jobClass,
                    'spooler_name' =>$spooler_name,
                    'date' => new \DateTime($date)
                )
            );

            if (!$JobDay) {
                $JobDay = new \Arii\ReportBundle\Entity\JOBDay();
                $new++;
            }
*/
            $JobDay = new \Arii\ReportBundle\Entity\JOBDay();
            $new++;
            
            $JobDay->setApp($app);            
            $JobDay->setEnv($env);
            $JobDay->setJobClass($jobClass);
            $JobDay->setSpoolerName($spooler_name);
            $JobDay->setDate(new \DateTime($date));                        
            $JobDay->setJobs($jobs);
            
            $id =  "$app#$env#$jobClass#$spooler_name#$date";         
            if (isset($Created[$id]))
                $JobDay->setCreated($Created[$id]);            
            if (isset($Deleted[$id]))
                $JobDay->setDeleted($Deleted[$id]);            
            $em->persist($JobDay);        
        }        
        $em->flush();        

        return new Response("JobDays ($new) [".(time()-$debut)."]");
    }
    
    /* Agrégation des jobs par mois */
    public function JobMonthAction()
    {
        set_time_limit(3600);        
        ini_set('memory_limit','-1');
        if ($this->container->has('profiler')) {
            $this->container->get('profiler')->disable();
        }

        $debut = time();
        
        $em = $this->getDoctrine()->getManager();
        // Il faut reparser tous les jobs
        // limiter par spooler ? 
        $Jobs = $em->getRepository("AriiReportBundle:JOBDay")->findJobsMonth();
        $new = $upd = 0;
        foreach ($Jobs as $Job) {
            
            $app = $Job['app'];
            $env = $Job['env'];
            $class = $Job['jobClass'];            
            $month = $Job['month'];
            $year = $Job['year'];
            $spooler_name = $Job['spooler_name'];
            $jobs = $Job['jobs'];
            $created = $Job['created'];
            $deleted = $Job['deleted'];
            
            // truncate ?! si on truncate on perd l'historique
            $JobMonth = $em->getRepository("AriiReportBundle:JOBMonth")->findOneBy(
                array( 'app'=>$app, 'env' =>$env, 'spooler_name' => $spooler_name, 'month' => $month, 'year' => $year
            ));

            if (!$JobMonth) {
                $JobMonth = new \Arii\ReportBundle\Entity\JOBMonth();
                $new++;
            }
            else {
                $upd++;
            }
            
            $JobMonth->setApp($app);            
            $JobMonth->setEnv($env);
            $JobMonth->setJobClass($class);
            $JobMonth->setMonth($month);
            $JobMonth->setYear($year);
            
            $JobMonth->setSpoolerName($spooler_name);
            
            $JobMonth->setJobs($jobs);
            $JobMonth->setCreated($created);
            $JobMonth->setDeleted($deleted);
            
            $em->persist($JobMonth);        
        }        
        $em->flush();        

        return new Response("JobMonths new($new) update($upd) [".(time()-$debut)."]");
    }
    
    /* COMPLEMENTS */
    public function RulesAction()
    {                  
        $debut=time();
        set_time_limit(3600);
        if ($this->container->has('profiler'))
            $this->container->get('profiler')->disable();        
        
        // on recupere les regles par niveaux
        
        $Rules = $this->getDoctrine()->getRepository("AriiReportBundle:JOBRule")->findBy( [],[ 'priority' => 'ASC' ]);
        $Trans = [];
        foreach ($Rules as $Rule) {
            array_push( $Trans,
                [
                    'priority' => $Rule->getPriority(),
                    'input' => $this->Rule2Array($Rule->getInput()),
                    'output' => $this->Rule2Array($Rule->getOutput())
                ]
            );            
        }

        // On traite les jobs
        $em = $this->getDoctrine()->getManager();
        $Jobs = $this->getDoctrine()->getRepository("AriiReportBundle:JOB")->findAll( [], [ 'job_name' => 'ASC' ]);        
        foreach ($Jobs as $Job) {
            $Info['job_name'] = $Job->getJobName();
            $Info['job_type'] = $Job->getJobType();

            // Application des règles 
            foreach ($Trans as $T) {
                $found=true;
                $Values = [];
                $n=0; // nombre de remplacement
                foreach ($T['input'] as $var => $val) {
                    if (!isset($Info[$var])) 
                        throw new \Exception("'$var' ?!");
                    if (preg_match("/$val/",$Info[$var],$Matches)) {
                        array_shift($Matches);
                        foreach($Matches as $Match) {
                            $n++;
                            $Values[$n] = $Match;
                        }
                    }
                    else {
                        $found=false;
                    }
                }
                if ($found) {
                    print $Info['job_name']." ";
                    foreach ($T['output'] as $var => $val) {
                        if (preg_match("/\{\{(\d*)\}\}/",$val,$Matches)) {
                            array_shift($Matches);
                            foreach ($Matches as $Match) {
                                $val = str_replace('{{'.$Match.'}}',$Values[$Match],$val);
                            }
                        }
                        print "[$var=>$val]";
                        switch($var) {
                            case 'app':
                                $Job->setApp($val);
                                break;
                            case 'env':
                                $Job->setEnv($val);
                                break;
                            case 'class':
                                $Job->setJobClass($val);
                                break;
                            case 'job_type':
                                $Job->setJobType($val);
                                break;
                            default:
                                throw new \Exception("'$var' ?!");
                        }
                        $em->persist($Job);
                    }
                    print "\n";
                }
            }
        }
        $em->flush();        
        return new Response("Ok!");
    }
    
    private function Rule2Array($rule) {
        $Result = [];
        $Members = explode('&',$rule);
        foreach ($Members as $Member) {
            if (($p=strpos($Member,'='))>0) {
                $var = substr($Member,0,$p);
                $val = substr($Member,$p+1);
                $Result[$var] = $val ;
            }
        }
        return $Result;
    }

    // SQL
    public function RulesOLDAction()
    {
        $debut=time();
        set_time_limit(3600);
        if ($this->container->has('profiler'))
            $this->container->get('profiler')->disable();        
        
        $portal = $this->container->get('arii_core.portal');
        $Rules = $portal->getRules();        

        $em = $this->getDoctrine()->getManager();
        foreach ($Rules as $job=>$Values) {            
            print "$job: ".$em->getRepository("AriiReportBundle:JOB")->UpdateAppEnv($job,$Values['OutApp'],$Values['OutEnv'])."\n";
        }
        return new Response("Ok!");
    }
    
}

