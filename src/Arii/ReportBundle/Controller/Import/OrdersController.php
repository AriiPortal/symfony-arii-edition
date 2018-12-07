<?php
namespace Arii\ReportBundle\Controller\Import;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrdersController extends Controller
{

    public function dbAction($db,$_route)
    {  
        $limit=5000;
        
        $time=time();
        set_time_limit(300);
        $ojs = $this->getDoctrine()->getManager($db);
        $em = $this->getDoctrine()->getManager();
        
        // On recupere les infos de la derniere synchro
        $Sync = $em->getRepository("AriiReportBundle:Sync")->findOneBy([
                'db_name' => $db,
                'entity'  => 'SchedulerOrderStepHistory',
                'route'   => $_route
        ]);

        // Si il existe
        if ($Sync) {
            $last_id = (int) $Sync->getLastId();       
        }
        else {
            $Sync = new \Arii\ReportBundle\Entity\Sync();
            $Sync->setName('JobScheduler');
            $Sync->setDbName($db);
            $Sync->setEntity('SchedulerOrderStepHistory');
            $Sync->setRoute($_route);
            $Sync->setCheckPoint('HISTORY_ID');
            // On cherche l'id le plus ancien
            $last_id = (int) $ojs->getRepository("AriiJIDBundle:SchedulerOrderStepHistory")->firstId();
            $Sync->setLastId($last_id);
        }
        print $last_id;
        print " -> ";
        print $ojs->getRepository("AriiJIDBundle:SchedulerOrderStepHistory")->lastId();
        
        // Referentiel des jobs     
        // derniers historiques
        $Runs = $ojs->getRepository("AriiJIDBundle:SchedulerOrderStepHistory")->synchro($last_id,$limit);
        $n = 0;
        if ($Runs) {
            foreach ($Runs as $Run) {

                // On cherche l'ordre 
                $Order = $ojs->getRepository("AriiJIDBundle:SchedulerOrderHistory")->find($Run['idOrder']);
                if (!$Order)
                    continue;
                
                // ordre fixe ou non ?
                $title      = $Order->getTitle();
                $order_id   = $Order->getOrderId();
                $spooler_id = $Order->getSpoolerId();
                $job_chain =  $Order->getJobChain();
                print "($spooler_id | $job_chain | $order_id)\n";

                if ((substr($title,0,1) != '#') 
                        and (strpos($title,'/')===false)
                        and (!is_numeric($order_id))
                        ) 
                    $Find = [
                    'spoolerId' => $spooler_id,
                    'jobChain'  => $job_chain,
                    'orderId'   => $order_id 
                    ];
                else 
                    $Find = [
                    'spoolerId' => $spooler_id,
                    'jobChain'  => $job_chain
                    ];

                // On cherche l'ordre en cours dans la base actuelle
                $Activity = $em->getRepository("AriiReportBundle:ActivityStatus")->findOneBy($Find);
                // si l'activite est inconnue, on la cree
                if (!$Activity) {
                    $Activity = new \Arii\ReportBundle\Entity\ActivityStatus();
                }

                // On met a jour le stats
                $Activity->setTitle($title);
                $Activity->setOrderId($order_id);
                $Activity->setSpoolerId($spooler_id);
                $Activity->setJobChain($job_chain);

                $Activity->setLog(          $Order->getLog());            
                $Activity->setStartTime(    $Order->getStartTime());
                $Activity->setEndTime(      $Order->getEndTime());
                $Activity->setState(        $Order->getState());
                $Activity->setStateText(    $Order->getStateText());

                $Activity->setIdDb($Run['idOrder']);            

                $em->persist($Activity);

                // On stocke le run
                // On cherche l'ordre 
                $OJSRun = $ojs->getRepository("AriiJIDBundle:SchedulerHistory")->find($Run['idHistory']);
                // si il n'y a pas d'ordre, on passe
                if ($OJSRun) {                
                    // On verifie qu'li n'est pas deja stocké ? ou index ?
                    $History = $em->getRepository("AriiReportBundle:ActivityRuns")->findOneBy([
                        'activity'  => $Activity,
                        'step'      => $Run['step']
                    ]);
                    // si pas de runs on ajoute
                    if (!$History) {
                        $History = new \Arii\ReportBundle\Entity\ActivityRuns();
                    }            
                    // On remplit
                    $History->setActivity( $Activity );
                    $History->setStep( $Run['step'] );

                    $History->setLog(          $OJSRun->getLog());            
                    $History->setStartTime(    $OJSRun->getStartTime());
                    $History->setEndTime(      $OJSRun->getEndTime());
                    $History->setError(        $OJSRun->getError());
                    $History->setErrorCode(    $OJSRun->getErrorCode());
                    $History->setErrorText(    $OJSRun->getErrorText());
                    $History->setSteps(        $OJSRun->getSteps());
                    $History->setJobName(      $OJSRun->getJobName());
                    $History->setParameters(   $OJSRun->getParameters());

                    $History->setIdOrder(      $Run['idOrder']);
                    $History->setIdHistory(    $Run['idHistory']);

                    $em->persist($History);
                    $em->flush();

                    $n++;
                }
                $last_id = $Run['idHistory'];
            } 
        }
        else {
            $last_id += $limit;
        }
        $duration = (time()-$time);
        
        // On met a jour la table de synchro
        $Sync->setDuration($duration);
        $Sync->setNbLines($n);
        $Sync->setLastId($last_id);
        $Sync->setLastUpdate(new \DateTime());

        $em->persist($Sync);
        $em->flush();
        
        return new Response(sprintf( "# %d\n%d s\n%s\n",$n,(time()-$time),"success"));    
    }
    
    public function dbJobAction($db)
    {  
        $time=time();
        set_time_limit(300);
        $ojs = $this->getDoctrine()->getManager($db);
        $em = $this->getDoctrine()->getManager();
        
        // Referentiel des jobs
        $n = 0;
        $date = new \DateTime();
        $date->modify('-4 day');

        $Records = $ojs->getRepository("AriiJIDBundle:SchedulerHistory")->synchroHistory($date);
        $Done=[];
        foreach ($Records as $Record) {
            $instance   = $Record['spoolerId'];
            $job_name   = $Record['jobName'];
            
            $name = "$instance#$job_name";
            if (isset($Done[$name])) continue;
            
            $Status = $em->getRepository("AriiACKBundle:Status")->findOneBy([ 'name' =>   $name ]);
            if (!$Status) {
                $Status = new \Arii\ACKBundle\Entity\Status();
                $Status->setState('OPEN');
            }       
            $Status->setInstance ($instance);            
            $Status->setName     ($name);
            $Status->setSource   ('OJS');            
            $Status->setType     ('JOB');
            $Status->setTitle    ($job_name);
            $Status->setLastStart($Record['startTime']);
            $Status->setLastEnd  ($Record['endTime']);
            $Status->setExitCode ($Record['exitCode']);
            $Status->setMessage  ($Record['errorText']);
            if ($Record['log']) {
                $log = utf8_encode( gzinflate ( substr( stream_get_contents($Record['log']), 10, -8) ) );
                
                # Découpage debut et fin ?
                $log = substr($log,0,10240);

                if ($Record['log'])
                    $Status->setJobLog   ( $log );
            }
            else $Status->setJobLog(null);
            
            if ($Record['error']>0) {
                $Status->setStatus  ('ERROR');
                $Status->setStatusTime($Record['endTime']);               
            }
            else {
                $Status->setStatus  ('SUCCESS');
                $Status->setState('CLOSE');
                $Status->setStatusTime($Record['endTime']);
            }
            $Status->setStateTime($Record['endTime']);
            
            $Status->setUpdated  (new \DateTime());
            
            $em->persist($Status);
            if ($n++ % 10 == 0)
                $em->flush();
            $Done[$name] = 1;
            print "DONE $name<br/>";
        } 
        $em->flush();
        return new Response(sprintf( "# %d\n%d s\n%s\n",$n,(time()-$time),"success"));    
    }
    
    public function purgeAction()
    {  
        set_time_limit(300);
                
        // On traite le log
        if (isset($_FILES['txt']['tmp_name']))
            $log = file_get_contents($_FILES['txt']['tmp_name']);
        else 
            $log = file_get_contents('../workspace/ACK/Input/Autosys/status.txt');

        // Info supplementaires
        $request = Request::createFromGlobals();
        $source = $request->get('source');
        
        $Infos = explode("\n",$log);
        do {
            $head = array_shift($Infos);
        } while (substr($head,0,10)!='__________');
        $head = array_shift($Infos);
        
        $em = $this->getDoctrine()->getManager();
        $n = 0;
        foreach ($Infos as $info) {
            $job_name   = trim(substr($info,0,64));
            $last_start = trim(substr($info,65,20));
            $last_end   = trim(substr($info,86,20));
            $status     = trim(substr($info,107,2));
            $run        = str_replace('/','.',trim(substr($info,110,9)));
            $exit       = trim(substr($info,119,9));
            
            $record = $this->getDoctrine()->getRepository("AriiACKBundle:Status")->findOneBy(
            [
                'source' => $source,
                'name'   => $job_name
            ]);
            
            if (!$record)
                $record = new \Arii\ACKBundle\Entity\Status();
            
            $record->setSource               ($source);
            $record->setName                 ($job_name);
            $record->setType                 ('ATS');
            $record->setTitle                ($source.' '.$job_name);
            $record->setLastStart            (($last_start == '-----')?null:new \DateTime($last_start));
            $record->setLastEnd              (($last_end == '-----')?null:new \DateTime($last_end));
            $record->setExitCode             ($exit);
            $record->setRunTry               ($run_try);
            $record->setStatus               (isset($this->Status[$status])?$this->Status[$status]:'UNKNOWN');
            $record->setUpdated              (new \DateTime());
            
            $em->persist($record);
            if ($n++ % 100 == 0)
                $em->flush();            
        }
        $em->flush();
        return new Response("success");        
    }
    
}

