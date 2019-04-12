<?php

namespace Arii\ATSBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class AppServersController extends Controller
{
    public function listAction($repoId='ats_db')
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();        

        $em = $this->getDoctrine()->getManager($repoId);        
        $state = $this->container->get('arii_ats.state2');        
        $Schedulers = $state->AppServers($em);

        switch ($Filters['outputFormat']) {
            case 'dhtmlxGrid':
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Schedulers,'commAlias,hostname,maStatusId,statusTime,pid');        
                break;
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Schedulers, 'xml', SerializationContext::create()->setGroups(array('default')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Schedulers, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
        
    }

    public function pieAction() {
        $sql = $this->container->get('arii_core.sql');                  
        $qry = $sql->Select(array('TIME_STAMP'))
                .$sql->From(array('UJO_HA_PROCESS'))
                .$sql->OrderBy(array('HA_DESIGNATOR_ID'));

        $dhtmlx = $this->container->get('arii_core.dhtmlx');
        $data = $dhtmlx->Connector('data');

        $res = $data->sql->query($qry);
        $autosys = $this->container->get('arii_ats.autosys');
        $ok = $late = $ko = 0;
        while ($line = $data->sql->get_next($res))
        {
            $delay =  time() - $line['TIME_STAMP'];
            if ($delay > 300) {
                $ko++;
            }
            elseif ($delay > 30) {
                $late++;
            }
            else {
                $ok++;
            }
         }
        $pie = '<data>';
        $pie .= '<item id="RUNNING"><STATUS>RUNNING</STATUS><PROCESSES>'.$ok.'</PROCESSES><COLOR>#ccebc5</COLOR></item>';
        $pie .= '<item id="LATE"><STATUS>LATE</STATUS><PROCESSES>'.$late.'</PROCESSES><COLOR>#ffffcc</COLOR></item>';
        $pie .= '<item id="STOPPED"><STATUS>STOPPED</STATUS><PROCESSES>'.$ko.'</PROCESSES><COLOR>#fbb4ae</COLOR></item>';
        $pie .= '</data>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent( $pie );
        return $response;
    }

    
}