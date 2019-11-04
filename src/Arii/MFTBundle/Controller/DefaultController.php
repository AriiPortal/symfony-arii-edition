<?php

namespace Arii\MFTBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    
    public function indexAction()
    {       
        return $this->render('AriiMFTBundle:Default:index.html.twig' );
    }

    public function publicAction()
    {       
        return $this->render('AriiMFTBundle:Default:public.html.twig' );
    }
    
    public function readmeAction()
    {
        return $this->render('AriiMFTBundle:Default:readme.html.twig');
    }

    public function ribbonAction()
    { 
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');    
        return $this->render('AriiMFTBundle:Default:ribbon.json.twig',array(), $response );
    }

    public function ribbon_editAction()
    { 
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');    
        return $this->render('AriiMFTBundle:Default:ribbon_edit.json.twig',array(), $response );
    }

    public function structAction()
    { 
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');    
        return $this->render('AriiMFTBundle:Default:status.json.twig',array(), $response );
    }
    
    public function contextmenuAction()
    {        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');    
        return $this->render('AriiMFTBundle:Default:grid_menu.xml.twig',array(), $response );
    }

    public function toolbarAction()
    {        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');    
        return $this->render('AriiMFTBundle:Default:toolbar.xml.twig',array(), $response );
    }

    public function toolbar_activitiesAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');            
        return $this->render('AriiMFTBundle:Default:toolbar_activities.xml.twig',array(), $response  );
    }

    public function formAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        $sql = $this->container->get('arii_core.sql');    
        $sql->setDriver($this->container->getParameter('database_driver'));
        
        $qry = $sql->Select(array('ID','NAME','TITLE','DESCRIPTION','PARTNER_ID','STEP_START','STEP_END','CHANGE_TIME','CHANGE_USER','ENV'))
                .$sql->From(array('MFT_HISTORY'))
                .$sql->Where(array('ID' => $id));
        
        $dhtmlx = $this->container->get('arii_core.db');
        $data = $dhtmlx->Connector('form');
        $data->render_sql($qry,'ID','ID,NAME,TITLE,DESCRIPTION,PARTNER_ID,STEP_START,STEP_END,CHANGE_TIME,CHANGE_USER,ENV');
    }

    public function gridAction( $partner_id=-1, $sort='STATUS_TIME desc' )
    {
        $request = Request::createFromGlobals();
        if ($request->get('partner_id')>0)
            $partner_id = $request->get('partner_id');

        $history = $this->container->get('arii_mft.mft');
        $Transfers = $history->Status($partner_id,$sort);

        $grid = '<?xml version="1.0" encoding="UTF-8"?>';
        $grid .= "<rows>\n";
        $grid .= '<head>
            <afterInit>
                <call command="clearAll"/>
            </afterInit>
        </head>';
        
        foreach ($Transfers as $k=>$Transfer) {
            $grid .= '<row id="'.$k.'" style="background-color: '.$Transfer['COLOR'].'">';                
            $grid .= "<cell><![CDATA[".$Transfer['NAME']."]]></cell>";
            $grid .= "<cell><![CDATA[".$Transfer['TRANSFER']."]]></cell>";
            $grid .= "<cell>".$Transfer['STATUS']."</cell>";        
            $grid .= "<cell>".$Transfer['STATUS_TIME']."</cell>";            
            $grid .= "<cell>".$Transfer['OPERATION']."</cell>";
            $grid .= "<cell>".$Transfer['DELIVERY_STATUS']."</cell>";
            $grid .= "<cell>".$Transfer['START_TIME']."</cell>";            
            $grid .= "<cell>".$Transfer['END_TIME']."</cell>"; 
            $grid .= "<cell>".$Transfer['RUN']."</cell>";
            $grid .= "<cell>".$Transfer['PARTNER']."</cell>";
            $grid .= "<cell>".$Transfer['PROGRESS']."</cell>";            
            $grid .= "<cell>".$Transfer['TRANSFERRING']."</cell>"; 
            $grid .= "<cell>".$Transfer['NEXT_RUN']."</cell>"; 
            $grid .= "<userdata name='TRANSFER_ID'>".$Transfer['TRANSFER_ID']."</userdata>";
            $grid .= "<userdata name='PARTNER_ID'>".$Transfer['PARTNER_ID']."</userdata>";
            $grid .= "<userdata name='HISTORY_ID'>".$Transfer['HISTORY_ID']."</userdata>";
            $grid .= "<userdata name='DELIVERY_ID'>".$Transfer['DELIVERY_ID']."</userdata>";
            $grid .= "<userdata name='SCHEDULE_ID'>".$Transfer['SCHEDULE_ID']."</userdata>";
            $grid .= "</row>";
        }
        $grid.='</rows>';
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent( $grid );
        return $response;
    }

    public function pieAction() {
        $request = Request::createFromGlobals();

        $mft = $this->container->get('arii_mft.mft');        
        $Status = $mft->Status();

        $C['ABORT'] = $C['RUNNING'] = $C['SUCCESS'] = $C['STOPPED'] = $C['SKIPPED'] = $C['FAILURE']= $C['UNKNOWN'] = 0;
        foreach ($Status as $S) {
            $status = $S['STATUS'];
            if (isset($C[$status])) 
                $C[$status]++;
            else {
                $C['UNKNOWN']++;
            }
        }
        
        $pie = '<data>';
        $pie .= '<item id="ABORT"><TEXT>Abort</TEXT><STATUS>'.$C['ABORT'].'</STATUS><COLOR>'.$mft->ColorStatus('ABORT').'</COLOR></item>';
        $pie .= '<item id="SUCCESS"><TEXT>Ended</TEXT><STATUS>'.$C['SUCCESS'].'</STATUS><COLOR>'.$mft->ColorStatus('SUCCESS').'</COLOR></item>';
        $pie .= '<item id="RUNNING"><TEXT>Running</TEXT><STATUS>'.$C['RUNNING'].'</STATUS><COLOR>'.$mft->ColorStatus('RUNNING').'</COLOR></item>';
        $pie .= '<item id="SKIPPED"><TEXT>Skipped</TEXT><STATUS>'.$C['SKIPPED'].'</STATUS><COLOR>'.$mft->ColorStatus('SKIPPED').'</COLOR></item>';
        $pie .= '<item id="STOPPED"><TEXT>Stopped</TEXT><STATUS>'.$C['STOPPED'].'</STATUS><COLOR>'.$mft->ColorStatus('STOPPED').'</COLOR></item>';
        $pie .= '<item id="FAILURE"><TEXT>Failure</TEXT><STATUS>'.$C['FAILURE'].'</STATUS><COLOR>'.$mft->ColorStatus('FAILURE').'</COLOR></item>';
        $pie .= '<item id="UNKNOWN"><TEXT>Unknown</TEXT><STATUS>'.$C['UNKNOWN'].'</STATUS><COLOR>'.$mft->ColorStatus('UNKNOWN').'</COLOR></item>';
        $pie .= '</data>';
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent( $pie );
        return $response;
    }

    public function swaggerAction()
    {
        $portal = $this->container->get('arii_core.portal');
        return $this->render('AriiMFTBundle:Default:swagger.html.twig', [ 'db' => $portal->getDatabase() ]);
    }
    
}

