<?php
// src/Arii/MFTBundle/Controller/TransfersController.php

namespace Arii\MFTBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class TransfersController extends Controller
{

    public function indexAction()
    {
       return $this->render('AriiMFTBundle:Transfers:index.html.twig');
    }

    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        
        return $this->render('AriiMFTBundle:Transfers:toolbar.xml.twig',array(), $response );
    }

    public function formAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        
        return $this->render('AriiMFTBundle:Transfers:form.xml.twig',array(), $response );
    }

    public function contextmenuAction()
    {        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');    
        return $this->render('AriiMFTBundle:Transfers:grid_menu.xml.twig',array(), $response );
    }
    
    public function pieAction( $history_max=0, $onlyWarning=1 )
    {
        $request = Request::createFromGlobals();
        if ($request->get('history')>0) {
            $history_max = $request->get('history');
        }
        if ($request->get('onlyWarning')!='')
            $onlyWarning = $request->get('onlyWarning');

        $history = $this->container->get('arii_mft.history');
        $Transfers = $history->Transfers($history_max, $onlyWarning);
       
        $Status['success']=$Status['error']=$Status['running']=0;
        foreach ($Transfers as $k=>$Transfer) {
            $status = $Transfer['STATUS'];
            if (isset($Status[$status])) {
                $Status[$status]++;
            }
            else {
                $Status[$status]=0;
            }
        }
        
        $pie = '<data>';
        if ($onlyWarning==0)
            $pie .= '<item id="success"><STATUS>success</STATUS><JOBS>'.$Status['success'].'</JOBS><COLOR>#ccebc5</COLOR></item>';
        $pie .= '<item id="error"><STATUS>error</STATUS><JOBS>'.$Status['error'].'</JOBS><COLOR>#fbb4ae</COLOR></item>';
//        $pie .= '<item id="RUNNING"><STATUS>RUNNING</STATUS><JOBS>'.$Status['running'].'</JOBS><COLOR>#ffffcc</COLOR></item>';
  
        $pie .= '</data>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent( $pie );
        return $response;
    }
    
    public function gridAction( $partner_id=-1 )
    {
        $request = Request::createFromGlobals();
        if ($request->get('partner_id')>0)
            $partner_id = $request->get('partner_id');

        $history = $this->container->get('arii_mft.mft');
        $Transfers = $history->Transfers($partner_id);

        $grid = '<?xml version="1.0" encoding="UTF-8"?>';
        $grid .= "<rows>\n";
        $grid .= '<head>
            <afterInit>
                <call command="clearAll"/>
            </afterInit>
        </head>';
        foreach ($Transfers as $k=>$Transfer) {
            $grid .= '<row id="'.$k.'">';
            $grid .= "<cell><![CDATA[".$Transfer['PARTNER']."]]></cell>";
            $grid .= "<cell><![CDATA[".$Transfer['NAME']."]]></cell>";
            $grid .= "<cell><![CDATA[".$Transfer['TITLE']."]]></cell>";
            $grid .= "<cell><![CDATA[".$Transfer['DESCRIPTION']."]]></cell>";            
            $grid .= "<cell>".$Transfer['CHANGE_TIME']."</cell>";            
            $grid .= "<cell>".$Transfer['CHANGE_USER']."</cell>";            
            $grid .= "<cell>".$Transfer['ENV']."</cell>";            
            $grid .= "</row>";
        }
        $grid.='</rows>';
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent( $grid );
        return $response;
    }

    public function listAction( $partner_id=-1 )
    {
        $request = Request::createFromGlobals();
        if ($request->get('partner_id')>0)
            $partner_id = $request->get('partner_id');

        $mft = $this->container->get('arii_mft.mft');
        $Transfers = $mft->Transfers($partner_id,"TITLE");

        $grid = '<?xml version="1.0" encoding="UTF-8"?>';
        $grid .= "<rows>\n";
        $grid .= '<head>
            <afterInit>
                <call command="clearAll"/>
            </afterInit>
        </head>';
        foreach ($Transfers as $k=>$Transfer) {
            $grid .= '<row id="'.$k.'">';
            $grid .= "<cell><![CDATA[".$Transfer['TITLE']."]]></cell>";
            $grid .= "<cell><![CDATA[".$Transfer['PARTNER']."]]></cell>";
            $grid .= "<cell><![CDATA[".$Transfer['DESCRIPTION']."]]></cell>";            
            $grid .= "</row>";
        }
        $grid.='</rows>';
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent( $grid );
        return $response;
    }

    public function selectAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');
        $sql = $this->container->get('arii_core.sql');    
        
        $qry = $sql->Select(array('ID','TITLE'))
                .$sql->From(array('MFT_TRANSFERS'))
//                ." where PROTOCOL in ('sftp') "
                .$sql->OrderBy(array('TITLE'));
        
        $db = $this->container->get('arii_core.db');
        $data = $db->Connector('select');
        $data->render_sql($qry,'ID','ID,TITLE');
    }

}
