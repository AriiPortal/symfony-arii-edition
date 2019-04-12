<?php

namespace Arii\JOCBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class SpoolersController extends Controller
{
    
    public function indexAction()   
    {
        return $this->render('AriiJOCBundle:Spoolers:index.html.twig' );
    }

    public function formAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        return $this->render('AriiJOCBundle:Spoolers:form.json.twig',array(), $response );
    }

    public function menuAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render("AriiJOCBundle:Spoolers:menu.xml.twig", array(), $response);
    }

    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render("AriiJOCBundle:Spoolers:grid_toolbar.xml.twig", array(), $response);
    }
    
    public function listAction() {        
        $em = $this->getDoctrine()->getManager();
        $Spoolers = $em->getRepository("AriiJOCBundle:Spoolers")->findBy([],['updated' => 'ASC']);          
        $Status = [
            'RUNNING'   => 0,
            'PAUSED'    => 0,
            'LOST'      => 0
        ];    
        $Grid = [];
        foreach ($Spoolers as $Spooler) {
            $spooler_id = $Spooler->getName();
            $host = $Spooler->getHost();
            if ($spooler_id == $host)
                $name = $spooler_id;
            else
                $name = $spooler_id.'/'.$host;
            $status = strtoupper($Spooler->getState());
            $time = $Spooler->getUpdated()->format('Y-m-d H:i:s');
            $last = time() - strtotime( $time );
            if (($status=='RUNNING') and ($last>120))
                $status = 'LOST';
            $id  = $Spooler->getId();
            $Grid[$id] = [
                'id'        => $id,
                'spooler'   => $name,
                'status'    => $status,
                'updated'   => $time,
                'color'     => 'SPOOLER '.$status
            ];
            $Status[$status]++;
        }

        // On sauvegarde les resultats
        $Requests = $this->container->get('arii_core.requests');
        $Requests->writeStatus([
            'name'    => 'SPOOLERS LOST',
            'title'   => 'Spoolers not responding',
            'results' => $Status['LOST'],
            'status'  => 0
            ]
            , 'JOC');
        $Requests->writeStatus([
            'name'    => 'SPOOLERS PAUSED',
            'title'   => 'Spoolers PAUSED',
            'results' => $Status['PAUSED'],
            'status'  => 0
            ]
            , 'JOC');
        
        // On sauvegarde les infos
        $Render = $this->container->get('arii_core.render');
        return $Render->Grid($Grid,'spooler,status,updated','color');
    }

    public function pieAction() {
        $em = $this->getDoctrine()->getManager();
        $Spoolers = $em->getRepository("AriiJOCBundle:Spoolers")->findAll();          
        $Status = [
            'RUNNING'   => 0,
            'PAUSED'    => 0,
            'STOPPING'  => 0,
            'LOST'      => 0            
        ];        
        foreach ($Spoolers as $Spooler) {
            $status = strtoupper($Spooler->getState());
            $time = $Spooler->getUpdated()->format('Y-m-d H:i:s');
            $last = time() - strtotime( $time );
            if (($status=='RUNNING') and ($last>120))
                $status = 'LOST';
            $Status[$status]++;
        }
        
        $portal = $this->container->get('arii_core.portal');
        $ColorStatus = $portal->getColors();
        
        $pie = '<data>';
        foreach (['RUNNING','PAUSED','LOST'] as $status)
            $pie .= '<item id="'.$status.'"><STATUS>'.$status.'</STATUS><NB>'.$Status[$status].'</NB><COLOR>'.$ColorStatus["SPOOLER $status"]['bgcolor'].'</COLOR></item>';
        $pie .= '</data>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent( $pie );
        return $response;
        
    }
/* Fonctions à revoir */
    
    public function timelineAction()
    {
        $dhtmlx = $this->container->get('arii_core.dhtmlx');
        $data = $dhtmlx->Connector('scheduler');

        $session =  $this->container->get('arii_core.session'); 
        $this->refDate  =  $session->getRefDate();

//        $options = $dhtmlx->Connector('options');

        $sql = $this->container->get('arii_core.sql');
        $Fields = array (
            'spooler'    => 'sh.SPOOLER_ID',
            'job_name'   => 'sh.JOB_NAME',
            'error'      => 'sh.ERROR',
            'start_time' => 'sh.START_TIME',
            'end_time'   => 'sh.END_TIME' );

  /*        $qry = 'SELECT distinct sh.SPOOLER_ID as label, sh.SPOOLER_ID as value
                  FROM SCHEDULER_HISTORY sh
                  where not(sh.JOB_NAME="(Spooler)") and '.$sql->History($Fields).' order by sh.SPOOLER_ID';  
          $options->render_sql($qry,"section_id","value,label");
 */          
          $qry = 'SELECT sh.ID, sh.SPOOLER_ID as section_id, sh.JOB_NAME, sh.START_TIME, sh.END_TIME, sh.ERROR, sh.EXIT_CODE, sh.CAUSE, sh.PID, "green" as color  
                  FROM SCHEDULER_HISTORY sh
                  where not(sh.JOB_NAME="(Spooler)") and '.$sql->History($Fields).' order by sh.SPOOLER_ID, sh.JOB_NAME,sh.START_TIME';  

//          $data->set_options("section_id", $options );
          $data->event->attach("beforeRender", array( $this, "color_rows") );
          $data->render_sql($qry,"ID","START_TIME,END_TIME,JOB_NAME,color,section_id");
    }
    
    function color_rows($row){
        if ($row->get_value('END_TIME')=='') {
            $row->set_value("color", 'orange');
            $row->set_value("END_TIME", $this->refDate );
        }
        elseif ($row->get_value('ERROR')>0) {
            $row->set_value("color", 'red');
        }
    }    
    
    public function timeline_xmlAction()
    {
        $dhtmlx = $this->container->get('arii_core.dhtmlx');
        $data = $dhtmlx->Connector('scheduler');

       $session =  $this->container->get('arii_core.session'); 
        $this->refDate  =  $session->get('refDate');

        $sql = $this->container->get('arii_core.sql');
        
        $Fields = array (
            'spooler'    => 'soh.SPOOLER_ID',
            'job_chain'   => 'soh.JOB_CHAIN',
            'error'      => 'sosh.ERROR',
            'start_time' => 'soh.START_TIME',
            'end_time'   => 'soh.END_TIME' );

          $qry = 'SELECT soh.HISTORY_ID as ID, soh.SPOOLER_ID as section_id, soh.JOB_CHAIN, soh.START_TIME, soh.END_TIME, sosh.ERROR, "green" as fill  
                  FROM SCHEDULER_ORDER_HISTORY soh
                  left join SCHEDULER_ORDER_STEP_HISTORY sosh
                  on soh.HISTORY_ID=sosh.HISTORY_ID
                  where '.$sql->Filter($Fields).' order by soh.SPOOLER_ID, soh.JOB_CHAIN,soh.START_TIME';  

          $data->event->attach("beforeRender", array( $this, "color_rows") );
          $data->render_sql($qry,"ID","START_TIME,END_TIME,JOB_CHAIN,fill,section_id");
    }

    public function gridAction()
    {
        $request = Request::createFromGlobals();

        $State = $this->container->get('arii_joc.state');
        $Spoolers = $State->Spoolers();

        $list = '<?xml version="1.0" encoding="UTF-8"?>';
        $list .= "<rows>\n";
        $list .= '<head>
            <afterInit>
                <call command="clearAll"/>
            </afterInit>
        </head>';
        foreach ($Spoolers as $k=>$line) {
            // on cacule la couleur
            $status = $line['STATUS'];
            if (isset($this->ColorStatus[$status]))
                $color=$this->ColorStatus[$status];
            else 
                $color='white';
            $list .= '<row id="'.$line['DBID'].'" bgColor="'.$color.'">';
            $list .= '<cell>'.$line['SPOOLER'].'</cell>';
            $list .= '<cell>'.$status.'</cell>';
            $list .= '<cell>'.$line['VERSION'].'</cell>';
            $list .= '<cell>'.$line['HOST'].'</cell>';
            $list .= '<cell>'.$line['IP_ADDRESS'].'</cell>';
            $list .= '<cell>'.$line['TCP_PORT'].'</cell>';
            $list .= '<cell>'.$line['TIME'].'</cell>';
            $list .= '<cell>'.$line['LAST_UPDATE'].'</cell>';
            $list .= '<cell>'.$line['UPDATED'].'</cell>';
            $list .= '</row>';
            
            // si le temps est trop long, on tente nue connexion à la volée pour le prochain update
/*            if ($line['LAST_UPDATE']>30) {
                $focus = $this->container->get('arii_joc.focus2');        
                $focus->get($line['IP_ADDRESS'],$line['TCP_PORT'],'orders,job_chain_orders,job_orders,jobs,job_chains,schedules,remote_schedulers,payload,job_params',"WEB",-1);
            }
*/
        }
        $list .= "</rows>\n";
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent( $list );
        return $response;
    }
    
    public function barJobsAction()
    {
        $db = $this->container->get('arii_core.db');
        $data = $db->Connector('data');
        
        $sql = $this->container->get('arii_core.sql');
        $Fields = array (
            '{spooler}'    => 's.NAME' );

        $qry = $sql->Select(array('s.NAME as SPOOLER','j.STATE','count(j.ID) as NB'))
                .$sql->From(array('JOC_JOBS j'))
                .$sql->LeftJoin('JOC_SPOOLERS s',array('j.SPOOLER_ID','s.ID'))
                .$sql->Where($Fields)
                .$sql->GroupBy(array('s.NAME','j.STATE'))
                .$sql->OrderBy(array('s.NAME'));

        $res = $data->sql->query( $qry );
        while ($line = $data->sql->get_next($res)) {
                $spooler = $line['SPOOLER'];
                $state = $line['STATE'];
                $Bar[$spooler][$state] = $line['NB'];
        }
        $bar = "<?xml version='1.0' encoding='utf-8' ?>";
        $bar .= '<data>';
        foreach ($Bar as $k=>$States) {
            $bar .= '<item id="'.$k.'"><SPOOLER>'.$k.'</SPOOLER>';
            foreach (array('pending', 'running', 'stopped') as $state) {
                $bar .= "<$state>";
                if (isset($States[$state]))
                    $bar .= $States[$state];
                else
                    $bar .= '0';
                $bar .= "</$state>";
            }
            $bar .= '</item>';
        }
        $bar .= '</data>';        
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent( $bar );
        return $response;
    }
 
    public function barOrdersAction()
    {
        $db = $this->container->get('arii_core.db');
        $data = $db->Connector('data');
        
        $sql = $this->container->get('arii_core.sql');
        $Fields = array (
            '{spooler}'    => 's.NAME' );

        $qry = $sql->Select(array('s.NAME as SPOOLER','j.SUSPENDED','j.RUNNING','count(j.ID) as NB'))
                .$sql->From(array('JOC_ORDERS j'))
                .$sql->LeftJoin('JOC_SPOOLERS s',array('j.SPOOLER_ID','s.ID'))
                .$sql->Where($Fields)
                .$sql->GroupBy(array('s.NAME','j.SUSPENDED','j.RUNNING'))
                .$sql->OrderBy(array('s.NAME'));

        $res = $data->sql->query( $qry );
        while ($line = $data->sql->get_next($res)) {
                $spooler = $line['SPOOLER'];
                if ($spooler=='') continue;
                if (!isset($Bar[$spooler]['suspended']))
                    $Bar[$spooler]['suspended'] = 0;
                if (!isset($Bar[$spooler]['running']))
                    $Bar[$spooler]['running'] = 0;
                if (!isset($Bar[$spooler]['pending']))
                    $Bar[$spooler]['pending'] = 0;
                
                if (($line['SUSPENDED']>0) and ($line['RUNNING']>0)) {
                    $Bar[$spooler]['suspended'] += $line['NB'];
                    $Bar[$spooler]['running'] += $line['NB'];
                }
                else if ($line['SUSPENDED']>0) {
                    $Bar[$spooler]['suspended'] += $line['NB'];
                }
                else if ($line['RUNNING']>0) {
                    $Bar[$spooler]['running'] += $line['NB'];
                }
                else {
                    $Bar[$spooler]['pending'] += $line['NB'];
                }                        
        }
        $bar = "<?xml version='1.0' encoding='utf-8' ?>";
        $bar .= '<data>';
        foreach ($Bar as $k=>$States) {
            $bar .= '<item id="'.$k.'"><SPOOLER>'.$k.'</SPOOLER>';
            foreach (array('pending', 'running', 'suspended') as $state) {
                $bar .= "<$state>";
                if (isset($States[$state]))
                    $bar .= $States[$state];
                else
                    $bar .= '0';
                $bar .= "</$state>";
            }
            $bar .= '</item>';
        }
        $bar .= '</data>';        
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent( $bar );
        return $response;
    }

}
