<?php

namespace Arii\ATSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ProcessesController extends Controller
{
    public function indexAction()
    {
        $portal = $this->container->get('arii_core.portal');       
        return $this->render('AriiATSBundle:Processes:index.html.twig', [ 'db' => $portal->getDatabase() ]);
    } 
    
    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render("AriiATSBundle:Processes:toolbar.xml.twig", array(), $response);
    } 

    public function pieAction($onlyWarning=0) {
        $request = Request::createFromGlobals();
        if ($request->query->get( 'box' ))
            $box = $request->query->get( 'box' );      
        else 
            $box = '';
        if ($request->query->get( 'onlyWarning' )!='')
            $onlyWarning = $request->query->get( 'onlyWarning' );

        $state = $this->container->get('arii_ats.state');
        $Job = $state->Boxes($box,$onlyWarning);

        $autosys = $this->container->get('arii_ats.autosys');
        foreach ($Job as $k=>$j) {
            $status = $autosys->Status($j['STATUS']);
            if (isset($Status[$status])) 
                $Status[$status]++;
            else 
                $Status[$status] = 1;
        }
        $pie = '<data>';
        foreach (array('SUCCESS','FAILURE','TERMINATED','RUNNING','INACTIVE','ACTIVATED','WAIT_REPLY','ON_ICE','ON_HOLD','ON_NOEXEC') as $s) {
            list($bgcolor,$color) = $autosys->ColorStatus($s);
            if (isset($Status[$s])) 
                $pie .= '<item id="'.$s.'"><STATUS>'.$s.'</STATUS><JOBS>'.$Status[$s].'</JOBS><COLOR>'.$bgcolor.'</COLOR></item>';
        }
        $pie .= '</data>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent( $pie );
        return $response;
    }
    
}