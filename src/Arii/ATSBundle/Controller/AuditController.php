<?php

namespace Arii\ATSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AuditController extends Controller
{
    protected $images;
    protected $Color = array(
        "insert_job" => "#00cccc",
        "delete_job" => "#ff0033",
        "command"   => "white",
        "eoid"   => "white"        
        );
    
    public function __construct( )
    {
          $request = Request::createFromGlobals();
          $this->images = $request->getUriForPath('/../bundles/ariicore/images/wa');          
    }

    public function indexAction($db)
    {
        return $this->render('AriiATSBundle:Audit:index.html.twig', [ 'db' => $db ]);
    }
    
    public function toolbarAction($db)
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiATSBundle:Audit:grid_toolbar.xml.twig',array(), $response );
    }
    
/*
    [EOID] => VA10043wma00
    [STAMP] => 11.08.15
    [EVT_NUM] => 12995586
    [JOID] => 4078
    [JOB_VER] => 4
    [OVER_NUM] => -1
    [JOB_NAME] => LP.DIV.BOX.Journalier
    [BOX_NAME] =>  
    [AUTOSERV] => VA1
    [EVENT] => 122
    [EVENTTXT] => CHK_RUN_WINDOW
    [STATUS] => 0
    [STATUSTXT] => 
    [ALARM] => 0
    [ALARMTXT] => 
    [EVENT_TIME_GMT] => 1439398800
    [EXIT_CODE] => 0
    [QUE_STATUS] => 0
    [QUE_STATUS_STAMP] => 11.08.15
    [RUN_NUM] => 1229671
    [NTRY] => 0
    [TEXT] => 
    [MACHINE] => 
    [GLOBAL_KEY] => 
    [GLOBAL_VALUE] => 
    [WF_JOID] => 1
 */
    public function gridAction($db)
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();
        
        $em = $this->getDoctrine()->getManager($db);        
        $state = $this->container->get('arii_ats.state2');        
        $Audit = $state->AuditSendevent($em);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $list = '<?xml version="1.0" encoding="UTF-8"?>';
        $list .= "<rows>\n";
        $list .= '<head>
            <afterInit>
                <call command="clearAll"/>
            </afterInit>
        </head>';
        
        foreach ($Audit as $id=>$Info) {
            $list .= '<row id="'.$id.'">';            
            $list .= '<cell>'.$Info['date']->format('Y-m-d H:i:s').'</cell>';
            $list .= '<cell>'.$Info['event'].'</cell>';
            $list .= '<cell>'.$Info['job'].'</cell>';
            $list .= '<cell>'.$Info['status'].'</cell>';
            $list .= '<cell>'.$Info['global'].'</cell>';
            $list .= '<cell>'.$Info['user'].'</cell>';             
            $list .= '<cell>'.$Info['domain'].'</cell>';
            $list .= '<cell>'.$Info['comment'].'</cell>';
            $list .= '<cell>'.$Info['instance'].'</cell>';
            $list .= '</row>'; 
        }
        $list .= "</rows>\n";
        $response->setContent( $list );
        return $response;        
    }

    public function jobsAction($db)
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();
        
        $em = $this->getDoctrine()->getManager($db);        
        $state = $this->container->get('arii_ats.state2');        
        $Audit = $state->AuditJobs($em);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $list = '<?xml version="1.0" encoding="UTF-8"?>';
        $list .= "<rows>\n";
        $list .= '<head>
            <afterInit>
                <call command="clearAll"/>
            </afterInit>
        </head>';
        
        foreach ($Audit as $id=>$Info) {
            $list .= '<row id="'.$id.'">';            
            $list .= '<cell>'.$Info['date']->format('Y-m-d H:i:s').'</cell>';
            $list .= '<cell>'.$Info['event'].'</cell>';
            $list .= '<cell>'.$Info['job'].'</cell>';
            $list .= '<cell>'.$Info['user'].'</cell>';             
            $list .= '<cell>'.$Info['host'].'</cell>';
            $list .= '</row>'; 
        }
        $list .= "</rows>\n";
        $response->setContent( $list );
        return $response;        
    }
    
   public function DetailAction($db)
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        
        $em = $this->getDoctrine()->getManager($db);
        $state = $this->container->get('arii_ats.state2');        
        $Audit = $state->AuditJob($em,$id);
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $list = '<?xml version="1.0" encoding="UTF-8"?>';
        $list .= "<rows>\n";
        $list .= '<head>
            <afterInit>
                <call command="clearAll"/>
            </afterInit>
        </head>';
        
        foreach ($Audit as $id=>$Info) {
            $list .= '<row id="'.$id.'" style="color: '.$Info['color'].'">';            
            $list .= '<cell>'.$Info['attribute'].'</cell>';
            $list .= '<cell><![CDATA['.$Info['value'].']]></cell>';
            $list .= '<cell>'.$Info['is_edit'].'</cell>';
            $list .= '</row>'; 
        }
        $list .= "</rows>\n";
        $response->setContent( $list );
        return $response;        
    }

    public function pieAction($db) {
        $dhtmlx = $this->container->get('arii_core.dhtmlx');
        $data = $dhtmlx->Connector('data');
                
        // Jobs
        $Fields = array( '{job_name}'   => 'JOB_NAME' );
        
        $sql = $this->container->get('arii_core.sql');
        $qry = $sql->Select(array('m.ATTRIBUTE','count(m.AUDIT_INFO_NUM) as NB'))
                .$sql->From(array('UJO_AUDIT_INFO i'))
                .$sql->LeftJoin('UJO_AUDIT_MSG m',array('i.AUDIT_INFO_NUM','m.AUDIT_INFO_NUM'))
                .$sql->Where(array('{start_timestamp}'=> 'i.TIME','SEQ_NO'=>1))
                .$sql->GroupBy(array('m.ATTRIBUTE'));

        $res = $data->sql->query($qry);
        $autosys = $this->container->get('arii_ats.autosys');
        while ($line = $data->sql->get_next($res))
        {            
            $status = $autosys->Status($line['ATTRIBUTE']);
            $Status[$status] = $line['NB'];
        }
        $pie = '<data>';
        
        foreach ($Status as $s=>$nb) {
            if (isset($this->Color[$s]))
                $color=$this->Color[$s];
            else 
                $color='white';
            $pie .= '<item id="'.$s.'"><STATUS>'.$s.'</STATUS><JOBS>'.$nb.'</JOBS><COLOR>'.$color.'</COLOR></item>';
        }
        $pie .= '</data>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent( $pie );
        return $response;
    }

}