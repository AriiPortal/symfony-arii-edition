<?php

namespace Arii\ReportBundle\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class RunsController extends Controller
{
    
    public function indexAction()
    {
        $Filters = $this->container->get('report.filter')->getRequestFilter();
        return $this->render('AriiReportBundle:Dashboard\Runs:index.html.twig', $Filters );
    }

    public function gridAction()
    {
        $filter = $this->container->get('report.filter');
        list($env,$application,$start) = $filter->getAll();

        $em = $this->getDoctrine()->getManager();
        $Runs = $em->getRepository("AriiReportBundle:RUN")->findApplications(new \DateTime($start));
        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><rows>";
        foreach ($Runs as $run) {
            $xml .= '<row id="'.$run['application'].'">';
            $xml .= '<cell>'.$run['application'].'</cell>';
/*            $xml .= '<cell>'.$run['env'].'</cell>';
            $xml .= '<cell>'.$run['nb'].'</cell>';
*/            $xml .= '</row>';
        }
        $xml .= '</rows>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;            
    }
    
    public function executionsChartAction($limit=10)
    {
        $Filters = $this->container->get('report.filter')->getRequestFilter();

        $em = $this->getDoctrine()->getManager();
        $Runs = $em->getRepository("AriiReportBundle:RUNMonth")->findApplicationsExecutions($year*1,$month*1,$env);
        
        $portal = $this->container->get('arii_core.portal');
        $App = $portal->getApplications();

        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><data>";
        $nb=0;
        foreach ($Runs as $run) {
            if ($run['executions']>0) {
                $a = $run['application'];
                if (!isset($App[$a])) continue;
                if ($App[$a]['active']!=1) continue;
                $xml .= '<item id="'.$run['application'].'">';
                $xml .= '<application>'.$App[$a]['title'].'</application>';
                $xml .= '<alarms>'.$run['alarms'].'</alarms>';
                $xml .= '<executions>'.$run['executions'].'</executions>';
                $xml .= '</item>';
                $nb++;
            }
            if ($nb>=$limit) break;
        }
        $xml .= '</data>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;            
    }

    public function alarmsChartAction($limit=10)
    {
        $Filters = $this->container->get('report.filter')->getRequestFilter();

        $em = $this->getDoctrine()->getManager();
        $Runs = $em->getRepository("AriiReportBundle:RUNMonth")->findApplicationsAlarms($year,$month,$env);
        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><data>";
        $nb=0;
        foreach ($Runs as $run) {
            if ($run['alarms']>0) {
                $xml .= '<item id="'.$run['application'].'">';
                if ($run['title']=='')
                    $run['title']=$run['application'];
                $xml .= '<application>'.$run['title'].'</application>';
                $xml .= '<alarms>'.$run['alarms'].'</alarms>';
                $xml .= '<executions>'.$run['executions'].'</executions>';
                $xml .= '</item>';
                $nb++;
            }
            if ($nb>=$limit) break;
        }
        $xml .= '</data>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;            
    }
     
     public function StatusChartAction($app='',$env='',$mode='dashboard')
    {
        $Filters = $this->container->get('report.filter')->getRequestFilter();

        $em = $this->getDoctrine()->getManager();
        $Runs = $em->getRepository("AriiReportBundle:RUNMonth")->findExecutionsByMonth($start->format('Y')*100+$start->format('m'),$end->format('Y')*100+$end->format('m'),$env,$app);

        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><data>";
        foreach ($Runs as $run) {
            $id = $app.':'.$env.':'.sprintf("%04d-%02d",$run['year'],$run['month']);
            $xml .= '<item id="'.$id.'">';
            $xml .= '<mois>'.$this->get('translator')->trans('month.'.(substr('0'.$run['month'],-2)*1)).'</mois>';
            $xml .= '<executions>'.$run['executions'].'</executions>';
            $xml .= '<warnings>'.$run['warnings'].'</warnings>';
            $xml .= '<alarms>'.$run['alarms'].'</alarms>';
            $xml .= '<acks>'.$run['acks'].'</acks>';
            $xml .= '</item>';
        }
        $xml .= '</data>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;            
    }

    // bloqué sur 3 mois
    public function ApplicationsChartAction($app='',$env='',$mode='dashboard')
    {
        $Filters = $this->container->get('report.filter')->getRequestFilter();
        
        $em = $this->getDoctrine()->getManager();
        $Runs = $em->getRepository("AriiReportBundle:RUNMonth")->findApplicationsByMonths($start->format('Y')*100+$start->format('m'),$end->format('Y')*100+$end->format('m'),$env,$app);
        
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
        foreach ($Runs as $run) {
            $app = $run['application'];
            $date = sprintf("%04d%02d",$run['year'],$run['month']);
            if (isset($Mois[$date]))
            $order = $Mois[$date]['order'];
            $Chart[$app]['application'] = $app;
            $Chart[$app]['month'.$order] = $run['executions'];
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
            if (!isset($App[$a])) continue;
            if ($App[$a]['active']!=1) continue;
            $row .= '<application>'.$App[$a]['title'].'</application>';
            
            // On supprime les applications inactives
            $total = 0;
            if (isset($chart['month0']))
                $last = $chart['month0'];
            else 
                $last = 0; // nouvelle application
            $max_delta=0;
            for($i=1;$i<$n;$i++) { 
                if (isset($chart['month'.$i])) 
                    $exec = $chart['month'.$i];
                else
                    $exec = 0;
                $evolution = $exec - $last;
                $row .= '<month'.$i.'>'.$evolution.'</month'.$i.'>';
                if ($exec>0) {
                    $delta = abs($evolution/$exec);
                }
                else {
                    $delta = $exec;
                }
                if ($delta>$max_delta)
                    $max_delta = $delta;
                $row .= '<delta'.$i.'>'.$delta.'</delta'.$i.'>';                
                $last = $exec;
            }
            $row .= '<delta_max>'.$max_delta.'</delta_max>';                
            $row .= '</item>';
            // si  aucune evolution, on supprime la ligne
            $limit++;
            if (($limit<50) and ($max_delta>1))
                $xml .= $row;
        }
        $xml .= '</data>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;            
    }
    
    public function jobsAction($application='%',$env='P')
    {
        $request = Request::createFromGlobals();
        $filter = $this->container->get('report.filter');
        $scope = $request->query->get( 'scope' );
        list($app,$env,$date) = explode(':', $scope);
        list($year,$month) = explode('-', $date);

        list($env,$app,$dayPast,$day,$month,$year,$start,$end) = $filter->getFilter(
            $env, $app, -32, 1, $month, $year 
        );

        $em = $this->getDoctrine()->getManager();
        $Runs = $em->getRepository("AriiReportBundle:RUN")->findExecutionsByMonth($start,$end,$env,$app);
        
        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><rows>";
        foreach ($Runs as $run) {
            if ($run['ack']=='') { // prise en compte
                $color='#fbb4ae';
            }
            elseif ($run['ack_time']) { // prise en compte
                $color='#ccebc5';
            }
            else {
                $color='#ffffcc';
            }
            $xml .= '<row style="background-color: '.$color.';">';
            $xml .= '<cell>'.$run['job_name'].'</cell>';
            $xml .= '<cell>'.$run['start_time'].'</cell>';
            $xml .= '<cell>'.$run['end_time'].'</cell>';
            $xml .= '<cell>'.$run['times'].'</cell>';
            $xml .= '<cell>'.$run['status'].'</cell>';            
            $xml .= '<cell>'.$run['alarm'].'</cell>';
            $xml .= '<cell>'.$run['alarm_time'].'</cell>';
            $xml .= '<cell>'.$run['ack'].'</cell>';
            if (isset($run['ack_time']) and ($run['ack_time']!='') and is_object($run['ack_time']))
                $xml .= '<cell>'.$run['ack_time']->format('Y-m-d h:i:s').'</cell>';
            else 
                $xml .= '<cell/>';
            $xml .= '</row>';
        }
        $xml .= '</rows>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;            
    }

     public function StatusPerHourChartAction($app='*',$env='P',$mode='dashboard')
    {
        $Filters = $this->container->get('report.filter')->getRequestFilter();
        
        $em = $this->getDoctrine()->getManager();
        $R = $em->getRepository("AriiReportBundle:RUNHour")->findRuns($Filters['start'],$Filters['end'],$Filters['env'],$Filters['appl'],$Filters['jobClass']);
        $Runs = array();        
        foreach ($R as $run) {
            $id = $run['date']->format('Ymd').':'.sprintf("%02d",$run['hour']);
            $Runs[$id] = $run;
            $Runs[$id]['events']=0;
        } 
     
        // complement avec les evenements
        $Events = $em->getRepository("AriiCoreBundle:Event")->findEventsPerHour($Filters['start'],$Filters['end']);        
        foreach ($Events as $event) {
            $begin = clone $event['start_time'];
            $interval = $event['start_time']->diff( $event['end_time'] );
            $duree = $interval->h + $interval->days*24;
            for($i=0;$i<=round($duree);$i++) {
                $id = $begin->format('Ymd:H');
                if (!isset($Runs[$id])) {
                    $Runs[$id] = [
                        'date' => $begin,
                        'hour'  => $begin->format('H'),
                        'events' => 0
                    ];
                }
                $Runs[$id]['event']= $event['title'];
                $Runs[$id]['events']++;
                
                $begin->modify("+ 1 hour");                
            }                    
        }
        
        if ($Runs)
            ksort($Runs);
        
        $last = '';
        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><data>";   
        foreach ($Runs as $id=>$run) {
            // $time = $run['date']->format('Ymd:H');
            // $id = $app.':'.$env.':'.$time;
            $xml .= '<item id="'.$id.'">';
            $xml .= '<day>'.$run['date']->format('d').'</day>';
            $xml .= '<hour>'.$run['hour'].'</hour>';
            if (isset($run['env']))
                $xml .= '<env>'.$run['env'].'</env>';
            foreach (['executions','warning','alarms','acks','events'] as $f ) {
                if (isset($run[$f]))
                    $xml .= '<'.$f.'>'.$run[$f].'</'.$f.'>';
                else 
                    $xml .= '<'.$f.'/>';
            }
            // label
            if (isset($run['event']) and ($run['event']!=$last)) {
                $xml .= '<event>'.$run['event'].'</event>';
                $last = $run['event'];
            }
            else 
                $xml .= '<event/>';               
            $xml .= '</item>';
        }
        $xml .= '</data>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;            
    }
    
}

