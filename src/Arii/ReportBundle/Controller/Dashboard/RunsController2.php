<?php

namespace Arii\ReportBundle\Controller\Dashboard;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class RunsController extends Controller
{
    
    public function indexAction()
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
        
        return $this->render('AriiReportBundle:Dashboard\Runs:index.html.twig', 
            array( 
                'appl' => $app,
                'env' => $env,
                'month' => $month,
                'year' => $year,
                'dayPast' => $dayPast
                ) 
            );
    }

    public function gridAction()
    {
        $filter = $this->container->get('arii.filter');
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
    
    public function executionsChartAction($limit=5)
    {
        $request = Request::createFromGlobals();
        $filter = $this->container->get('arii.filter');
        list($env,$application,$dayPast,$day,$month,$year,$start,$end) = $filter->getFilter(
            $request->query->get( 'env' ),
            $request->query->get( 'app' ),
            -30,
            $request->query->get( 'day' ),
            $request->query->get( 'month' ),
            $request->query->get( 'year' )
        );

        $em = $this->getDoctrine()->getManager();
        $Runs = $em->getRepository("AriiReportBundle:RUNMonth")->findApplicationsExecutions($year,$month,$env);
        
        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><data>";
        $nb=0;
        foreach ($Runs as $run) {
            if ($run['executions']>0) {
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

    public function alarmsChartAction($limit=5)
    {
        $request = Request::createFromGlobals();
        $filter = $this->container->get('arii.filter');
        list($env,$application,$dayPast,$day,$month,$year,$start,$end) = $filter->getFilter(
            $request->query->get( 'env' ),
            $request->query->get( 'app' ),
            -30,
            $request->query->get( 'day' ),                
            $request->query->get( 'month' ),
            $request->query->get( 'year' )
        );

        $em = $this->getDoctrine()->getManager();
        $Runs = $em->getRepository("AriiReportBundle:RUNMonth")->findApplicationsAlarms($year,$month,$env);
        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><data>";
        $nb=0;
        foreach ($Runs as $run) {
            if ($run['executions']>0) {
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

    public function applicationsChartAction($limit=1000)
    {
        $request = Request::createFromGlobals();
        $filter = $this->container->get('arii.filter');
        list($env,$application,$dayPast,$day,$month,$year,$start,$end) = $filter->getFilter(
            $request->query->get( 'env' ),
            $request->query->get( 'app' ),
            -30,
            $request->query->get( 'day' ),
            $request->query->get( 'month' ),
            $request->query->get( 'year' )
        );

        $em = $this->getDoctrine()->getManager();
        $Runs = $em->getRepository("AriiReportBundle:RUNDay")->findRunsDay($start,$end,$env);

        $xml = "<?xml version='1.0' encoding='iso-8859-1'?><rows>";
        $nb=0;
        foreach ($Runs as $run) {
            if ($run['executions']>0) {
                $xml .= '<row id="'.$run['date']->format('Ymd').'#'.$run['application'].'">';
                $xml .= '<cell>'.$run['application'].'</cell>';
                $xml .= '<cell>'.$run['executions'].'</cell>';
                $xml .= '<cell>'.$run['alarms'].'</cell>';
                $xml .= '</row>';
                $nb++;
            }
            if ($nb>=$limit) break;
        }
        $xml .= '</rows>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');        
        $response->setContent( $xml );
        return $response;            
    }
    
     public function StatusChartAction($app='',$env='',$mode='dashboard')
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

    public function jobsAction($application='%',$env='P')
    {
        $request = Request::createFromGlobals();
        $filter = $this->container->get('arii.filter');
        $scope = $request->query->get( 'scope' );
        list($app,$env,$date) = explode(':', $scope);
        list($year,$month) = explode('-', $date);

        list($env,$app,$dayPast,$day,$month,$year,$start,$end) = $filter->getFilter( 
                $env, 
                $app, 
                -32, 
                1, 
                $month, 
                $year );

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

     public function StatusPerHourChartAction($app='',$env='',$mode='dashboard')
    {
        $filter = $this->container->get('arii.filter');
        $Params = $filter->getRequestFilter();

        $em = $this->getDoctrine()->getManager();
        $from = clone $Params['date']->modify('first day of this month');
        $to = clone $Params['date']->modify('last day of this month');
        
        $R = $em->getRepository("AriiReportBundle:RUNHour")->findExecutionsByHour($from,$to,$env,$app);
        foreach ($R as $run) {
            $id = $run['date']->format('Ymd').':'.sprintf("%02d",$run['hour']);
            $Runs[$id] = $run;
            $Runs[$id]['events']=0;
        } 
     
        // complement avec les evenements
        $Runs = array();
        $Events = $em->getRepository("AriiCoreBundle:Event")->findEventsPerHour($from,$to);        
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

