<?php

namespace Arii\SelfBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class QueuedController extends Controller
{
    public function indexAction()
    {
        return $this->render('AriiSelfBundle:Queued:index.html.twig');
    }

    public function menuAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiSelfBundle:History:menu.xml.twig',[], $response);
    }
    
    public function gridAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $list = '<?xml version="1.0" encoding="UTF-8"?>';
        $list .= '<rows>';
        $list .= '<head>
            <afterInit>
                <call command="clearAll"/>
            </afterInit>
        </head>';
        $Requests = $this->getDoctrine()->getRepository("AriiSelfBundle:Request")->findByStatus('QUEUED');
                  
        $portal = $this->container->get('arii_core.portal');
        $ColorStatus = $portal->getColors();
        
        $Host = [];
        foreach ($Requests as $Request) { 
            $status = $Request->getReqStatus();
            $list .= '<row id="'.$Request->getId().'" style="background-color: '.$ColorStatus[$status]['bgcolor'].';">';
            $list .= '<cell>'.$Request->getTitle().'</cell>';
            $list .= '<cell>'.$Request->getReference().'</cell>';
            $Parameters = $Request->getParameters();
            if ($Parameters) {
                $P = [];
                foreach($Parameters as $k=>$v) {
                    array_push($P,"$k=$v");
                }
                $list .= '<cell>'.implode(',',array_values($P)).'</cell>';            
            }
            else {
                $list .= '<cell/>';
            }
            $list .= '<cell>'.$status.'</cell>';
            if ($Request->getCreated())
                $list .= '<cell>'.$Request->getCreated()->format("Y-m-d h:i").'</cell>';     
            else 
                $list .= '<cell/>';
            if ($Request->getPlanned())
                $list .= '<cell>'.$Request->getPlanned()->format("Y-m-d h:i").'</cell>';     
            else 
                $list .= '<cell/>';
            if ($Request->getProcessed())
                $list .= '<cell>'.$Request->getProcessed()->format("Y-m-d h:i:s").'</cell>';     
            else 
                $list .= '<cell/>';
            if ($Request->getUsername())
                $list .= '<cell>'.$Request->getUsername().'</cell>';     
            else 
                $list .= '<cell/>';
            
            $list .= '</row>';
        }
        $list .= '</rows>';

        $response->setContent( $list );
        return $response;        
    }

    public function statusAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->get('id');

        // Demande terminée
        $Request = $this->getDoctrine()->getRepository("AriiSelfBundle:Request")->find($id);
        $History = $this->getDoctrine()->getRepository("AriiSelfBundle:History")->findBy([ 'request' => $Request],['processed' => 'DESC']);
        $H = [];
        foreach ($History as $histo) {
            array_push($H,[
                'started' => $histo->getStarted()->format('Y-m-d H:i'),
                'processed' => ($histo->getProcessed()?$histo->getProcessed()->format('Y-m-d H:i'):''),
                'log' => $histo->getRunLog(),
                'status' => $histo->getRunStatus(),
                'exit' => $histo->getRunExit(),
                'client_ip' => $histo->getClientIp()
            ]);
        }
        return $this->render('AriiSelfBundle:History:bootstrap.html.twig', [
            'name'  => $Request->getName(),
            'title' => $Request->getTitle(),
            'description' => $Request->getDescription(),
            'code'  => $Request->getRunCommand(),
            'username'  => $Request->getUsername(),
            'created' =>   $Request->getCreated()->format('Y-m-d h:i:s'),
            'processed' => ($Request->getProcessed()?$Request->getProcessed()->format('Y-m-d H:i'):''),
            'parameters' => $Request->getParameters(),
            'history' => $H
        ]);
    }
    
}
