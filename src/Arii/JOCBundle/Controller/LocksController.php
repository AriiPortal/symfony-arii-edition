<?php

namespace Arii\JOCBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class LocksController extends Controller {
    protected $ColorStatus = array( 
        "AVAILABLE" => "#ccebc5",
        "FREE" => "#ccebc5",
        "LOCKED" => "#fbb4ae",
        "READY" => "#ccebc5",
        "TRUE" => "#ccebc5",
        "SUSPENDED" => "red",
        "CHAIN STOP." => "red",
        "SPOOLER STOP." => "red",
        "SPOOLER PAUSED" => "#fbb4ae",
        "NODE STOP." => "red",
        "NODE SKIP." => "#ffffcc",
        "JOB STOP." => "#fbb4ae",        
        "RUNNING" => "#ffffcc",
        "WARNING" => "#fbb4ae",
        "FAILURE" => "#fbb4ae",
        "MISSING" => "red",
        "FALSE" => "#fbb4ae",
        "DONE" => "lightblue",
        "ENDED" => "lightblue",
        "ON REQUEST" => "lightblue",
        "FATAL" => 'red',
        'WAITING' => '#DDD',
        "SETBACK" => "#fbb4ae",
        'UNKNOWN!' => '#FFF',
        'USED' => '#fbb4ae'
        );

    protected $image;

    public function __construct( )
    {
        $request = Request::createFromGlobals();
        $this->images = $request->getUriForPath('/../arii/images/wa');
    }

    public function indexAction()
    {
        return $this->render("AriiJOCBundle:Locks:index.html.twig");
    }

   public function  toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiJOCBundle:Locks:grid_toolbar.xml.twig',array(), $response );
    }
    
    public function listAction()
    {
        $state = $this->container->get('arii.joc');
        list($Locks,$Status) = $state->getLocks($sort=['updated' => 'ASC']);
                
        $Render = $this->container->get('arii_core.render');
        return $Render->Grid($Locks,'SPOOLER,PATH,LOCK,STATUS,UPDATED','COLOR');
    }

    public function pieAction() {
        $state = $this->container->get('arii.joc');
        list($Locks,$Status) = $state->getLocks([]);
                
        $Render = $this->container->get('arii_core.render');
        return $Render->Pie($Status,'STATE','COLOR');
    }
        
    public function gridAction($sort='last')
    {
        $request = Request::createFromGlobals();        
        $nested = $request->get('chained');
        $onlyWarning = $request->get('onlyWarning');
        $sort = $request->get('sort');

        $state = $this->container->get('arii_joc.state');
        $Locks = $state->Locks();
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $list = '<?xml version="1.0" encoding="UTF-8"?>';
        $list .= "<rows>\n";
        $list .= '<head>
            <afterInit>
                <call command="clearAll"/>
            </afterInit>
        </head>';
        
        foreach($Locks as $id=>$line) {
            if ($line['STATE']=='active') {
                if ($line['IS_FREE']==1)
                    $status = 'FREE';
                else 
                    $status = 'LOCKED';
            }
            else {
                $status = strtoupper($line['STATE']);
            }
            $list .= '<row id="'.$id.'" bgColor="'.$this->ColorStatus[$status].'">';
            $list .= '<cell>'.$line['SPOOLER'].'</cell>';
            $list .= '<cell>'.$line['FOLDER'].'</cell>';
            $list .= '<cell>'.$line['NAME'].'</cell>';
            $list .= '<cell>'.$status.'</cell>';
            $list .= '<cell>'.$line['MAX_NON_EXCLUSIVE'].'</cell>';
            $list .= '</row>';
        }

        $list .= "</rows>\n";
        $response->setContent( $list );
        return $response;
    }

   public function jobsAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        $sql = $this->container->get('arii_core.sql');

        $dhtmlx = $this->container->get('arii_core.db');        
        // On retrouve le verrou
        $qry = $sql->Select(array('ID','SPOOLER_ID','PATH'))
                .$sql->From(array('JOC_LOCKS'))
                .$sql->Where(array('ID' => $id));
        $data = $dhtmlx->Connector('data');
        // Verifier qu'il y en a bien qu'un
        $res = $data->sql->query($qry);
        if (!$res) exit();
        $line = $data->sql->get_next($res);
        if (!$line) exit();
        
        $path = $line['PATH'];
        $spooler_id = $line['SPOOLER_ID'];
        
        $qry = $sql->Select(array('u.ID','EXCLUSIVE','IS_AVAILABLE','IS_MISSING','j.PATH as JOB','j.STATE'))
                .$sql->From(array('JOC_LOCKS_USE u'))
                .$sql->LeftJoin('JOC_JOBS j',array('u.JOB_ID','j.ID'))
                .$sql->Where(array('u.SPOOLER_ID' => $spooler_id, 'u.PATH' => $path));

        $data = $dhtmlx->Connector('grid');
        $data->event->attach("beforeRender",array($this,"use_render"));
        $data->render_sql($qry,'ID','JOB,STATE,EXCLUSIVE,IS_AVAILABLE,IS_MISSING');
    }

    public function useAction($sort='last')
    {
        $request = Request::createFromGlobals();
        $nested = $request->get('chained');
        $onlyWarning = $request->get('onlyWarning');
        $sort = $request->get('sort');

        $state = $this->container->get('arii_joc.state');
        $Locks = $state->LocksUse();
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $list = '<?xml version="1.0" encoding="UTF-8"?>';
        $list .= "<rows>\n";
        $list .= '<head>
            <afterInit>
                <call command="clearAll"/>
            </afterInit>
        </head>';
        
        foreach($Locks as $id=>$line) {
            if (!isset($line['RUNNING'])) 
                $line['RUNNING'] = 0;
            
            if ($line['IS_MISSING']==1) {
                $status = 'MISSING';
            }
            elseif ($line['RUNNING']>0) {
                $status = 'USED';
            }
            else {
                $status = 'AVAILABLE';
            }
            $list .= '<row id="'.$id.'" bgColor="'.$this->ColorStatus[$status].'">';
            $list .= '<cell>'.$line['SPOOLER'].'</cell>';
            $list .= '<cell>'.$line['FOLDER'].'</cell>';
            $list .= '<cell>'.$line['NAME'].'</cell>';
            $list .= '<cell>'.$status.'</cell>';
            if (isset($line['PENDING']))
                $list .= '<cell>'.$line['PENDING'].'</cell>';
            else
                $list .= '<cell/>';
            if (isset($line['RUNNING']))
                $list .= '<cell>'.$line['RUNNING'].'</cell>';
            else
                $list .= '<cell/>';
            $list .= '</row>';
        }

        $list .= "</rows>\n";
        $response->setContent( $list );
        return $response;
    }

    function use_render ($data){
        if ($data->get_value('IS_MISSING')==1) {
            $data->set_value('STATUS','MISSING');
        }
        elseif ($data->get_value('IS_AVAILABLE')==1) {
            $data->set_value('STATUS','AVAILABLE');
        }
        else {
            $data->set_value('STATUS','WAITING');
        }
    }

}

?>
