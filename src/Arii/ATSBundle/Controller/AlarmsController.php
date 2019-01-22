<?php

namespace Arii\ATSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AlarmsController extends Controller
{
    protected $images;
     
    public function __construct( )
    {
          $request = Request::createFromGlobals();
          $this->images = $request->getUriForPath('/../bundles/ariicore/images/wa');          
    }

    public function indexAction($db)
    {
        return $this->render('AriiATSBundle:Alarms:index.html.twig', [ 'db' => $db ]);
    }
    
    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiATSBundle:Alarms:grid_toolbar.xml.twig',array(), $response );
    }

    public function grid_menuAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiATSBundle:Alarms:grid_menu.xml.twig',array(), $response );
    }

    public function gridAction($db,$only_open=0)
    {
        $em = $this->getDoctrine()->getManager($db);        
        $state = $this->container->get('arii_ats.state2');        
        $Alarms = $state->Alarms($em);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $list = '<?xml version="1.0" encoding="UTF-8"?>';
        $list .= "<rows>\n";
        $list .= '<head>
            <afterInit>
                <call command="clearAll"/>
            </afterInit>
        </head>';

        foreach ($Alarms as $k=>$Alarm) 
        {
            $list .= '<row id="'.$Alarm['eoid'].'" bgColor="'.$Alarm['color'].'">';            
            $list .= '<cell>'.$Alarm['alarmTime']->format("Y-m-d h:m:s").'</cell>';
            $list .= '<cell>'.$Alarm['jobName'].'</cell>';
            $list .= '<cell>'.$Alarm['alarm'].'</cell>';
            $list .= '<cell>'.$Alarm['state_grid'].'</cell>';
            $list .= '<cell>'.$Alarm['status'].'</cell>';
            $list .= '<cell>'.$Alarm['theUser'].'</cell>';
            $list .= '<cell><![CDATA['.$Alarm['eventComment'].']]></cell>';
            $list .= '<cell><![CDATA['.$Alarm['nb'].']]></cell>';
            $list .= '</row>'; 
        }
        $list .= "</rows>\n";
        $response->setContent( $list );
        return $response; 
    }

    public function DetailAction($db)
    {
        $request = Request::createFromGlobals();
        $eoid= $request->query->get( 'id' );
            
        $em = $this->getDoctrine()->getManager($db);        
        $state = $this->container->get('arii_ats.state2');        
        list($Alarm) = $em->getRepository("AriiATSBundle:UjoAlarm")->findAlarm( $eoid );
        
        $autosys = $this->container->get('arii_ats.autosys');
        
        $xml = "<?xml version='1.0' encoding='iso-8859-1'?>";
        $xml .= '<data>';
        foreach(array('response','jobName') as $k) {
            if (isset($Alarm[$k]))
                $xml .= '<'.$k.'>'.$Alarm[$k].'</'.$k.'>';
        }
        $xml .= '<status>'.$autosys->Status($Alarm['status']).'</status>';
        $xml .= "</data>\n";
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent( $xml );
        return $response;     
    }

    public function pieAction($only_warning=0) {
        $request = Request::createFromGlobals();
        if ($request->query->get( 'only_warning' ))
            $only_warning=$request->query->get( 'only_warning');

        $dhtmlx = $this->container->get('arii_core.dhtmlx');
        $data = $dhtmlx->Connector('data');
                
        // Jobs
        $sql = $this->container->get('arii_core.sql');
        $qry = $sql->Select(array('a.STATE','count(a.EOID) as NB'))
                .$sql->From(array('UJO_ALARM a'))
                .$sql->LeftJoin('UJO_JOB j',array('a.JOID','j.JOID'))
                .$sql->Where(array('{start_timestamp}'=> 'a.ALARM_TIME', 'j.IS_ACTIVE' => 1, '{job_name}'   => 'j.JOB_NAME'))
                .$sql->GroupBy(array('a.STATE'));

        $res = $data->sql->query($qry);
        $autosys = $this->container->get('arii_ats.autosys');
        $Status["OPEN"] = $Status["ACKNOWLEDGED"] = $Status["CLOSED"] = 0;
        while ($line = $data->sql->get_next($res))
        {            
            $status = $autosys->AlarmState($line['STATE']);         
            if ($only_warning and ($status == "CLOSED")) 
                $Status[$status] = 0;
            else
                $Status[$status] = $line['NB'];
        }
        $pie = '<data>';
        
        foreach ($Status as $s=>$nb) {
            list($bgcolor,$color) = $autosys->ColorStatus($s);
            $pie .= '<item id="'.$s.'"><STATUS>'.$s.'</STATUS><JOBS>'.$nb.'</JOBS><COLOR>'.$bgcolor.'</COLOR></item>';
        }
        $pie .= '</data>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent( $pie );
        return $response;
    }

}