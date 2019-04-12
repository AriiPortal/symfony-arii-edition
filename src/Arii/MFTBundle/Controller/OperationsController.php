<?php
// src/Arii/MFTBundle/Controller/OperationsController.php

namespace Arii\MFTBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class OperationsController extends Controller
{
    public function indexAction()
    {
       return $this->render('AriiMFTBundle:Operations:index.html.twig');
    }

    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        
        return $this->render('AriiMFTBundle:Operations:toolbar.xml.twig',array(), $response );
    }

    public function formAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        
        return $this->render('AriiMFTBundle:Operations:form.xml.twig',array(), $response );
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
        $Operations = $history->Operations($history_max, $onlyWarning);
       
        $Status['success']=$Status['error']=$Status['running']=0;
        foreach ($Operations as $k=>$Transfer) {
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
    
    public function gridAction( $transfer_id=-1 )
    {
        $request = Request::createFromGlobals();
        if ($request->get('transfer_id')!='')
            $transfer_id = $request->get('transfer_id');

        $history = $this->container->get('arii_mft.mft');
        $Operations = $history->Operations($transfer_id);

        $grid = '<?xml version="1.0" encoding="UTF-8"?>';
        $grid .= "<rows>\n";
        $grid .= '<head>
            <afterInit>
                <call command="clearAll"/>
            </afterInit>
        </head>';
        foreach ($Operations as $k=>$Transfer) {
            $grid .= '<row id="'.$k.'">';
            $grid .= "<cell>".$Transfer['TRANSFER']."</cell>";
            $grid .= "<cell>".$Transfer['STEP']."</cell>";
            $grid .= "<cell>".$Transfer['NAME']."</cell>";
            $grid .= "<cell>".$Transfer['TITLE']."</cell>";
            $grid .= "<cell><![CDATA[".$Transfer['DESCRIPTION']."]]></cell>";            
            $grid .= "<cell>".$Transfer['SOURCE']."</cell>";
            $grid .= "<cell>".$Transfer['TARGET']."</cell>";
            $grid .= "<cell>".$Transfer['ENV']."</cell>";            
            $grid .= "</row>";
        }
        $grid.='</rows>';
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent( $grid );
        return $response;
    }
    
}
