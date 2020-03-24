<?php

namespace Arii\ATSBundle\Controller\APIDB;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ActionsController extends Controller
{
    public function listAction($repoId='ats_db')
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();
        
        $em = $this->getDoctrine()->getManager($repoId);        
        $state = $this->container->get('arii_ats.state');        
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
}