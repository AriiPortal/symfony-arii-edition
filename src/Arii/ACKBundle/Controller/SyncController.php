<?php

namespace Arii\ACKBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class SyncController extends Controller{

    public function indexAction()
    {
        return $this->render('AriiACKBundle:Sync:index.html.twig');
    }

    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render("AriiACKBundle:Sync:toolbar.xml.twig", array(), $response);
    }
    
    public function gridAction()
    {
        $Syncs = $this->getDoctrine()->getRepository('AriiReportBundle:Sync')->findAll();
        $Grid = [];
        foreach ($Syncs as $Sync) {
            // on calcule le délai 
            $Now = new \DateTime();
            $diff = round(($Now->getTimestamp() - $Sync->getLastUpdate()->getTimestamp())/60);
            if ($diff>10) {
                $on_time = 'ERROR';
            }
            elseif ($diff>2) {
                $on_time = 'WARNING';
            }
            else {
                $on_time = 'SUCCESS';
            }
            
            array_push($Grid, [
                'id'          => $Sync->getId(),
                'name'        => $Sync->getName(),
                'db_name'     => $Sync->getDbName(),
                'lines'       => $Sync->getNbLines(),
                'duration'    => $Sync->getDuration(),
                'last_update' => $Sync->getLastUpdate(),
                'delay'       => $diff,
                'on_time'     => $on_time                
            ]);
        } 
        
        $render = $this->container->get('arii_core.render');     
        return $render->grid($Grid,'name,db_name,last_update,duration,lines,delay','on_time');
    }

    public function countAction()
    {
        return new Response( '{ count: "'.$this->getDoctrine()->getRepository('AriiACKBundle:Status')->getNb().'" }' );
    }
   
}

?>
