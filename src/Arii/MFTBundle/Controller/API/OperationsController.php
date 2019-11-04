<?php

namespace Arii\MFTBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class OperationsController extends Controller
{
    public function getAction($transferId)
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();        

        $em = $this->getDoctrine()->getManager();        
        $state = $this->container->get('arii_mft.mft');        
        $Operations = $state->TransferOperations($em,$transferId,$Filters);
        
        switch ($Filters['outputFormat']) {
            case 'png':
                $uml = "@startuml\nskinparam monochrome true\n";
                $n=0;
                if ($Operations) {
                    foreach ($Operations as $Operation) {
                        if (($Operation['source']!='') and ($Operation['target']!='')) {
                            # Deplacement ?
                            if (isset($Operation['parameters']['remove_files']) and ($Operation['parameters']['remove_files']))
                                $move = '-->';
                            else 
                                $move = '->';
                            $uml .= "\"".$Operation['source']['title']."\" $move \"".$Operation['target']['title']."\" : ".str_replace('_',' ',$Operation['title'])."\n";
                            
                            # Une petite note ?
                            $Note = [];
                            if (isset($Operation['parameters']['mail']['on_success']['mail_to'])) {
                                array_push($Note, 'Mail on success to '.$Operation['parameters']['mail']['on_success']['mail_to']);
                            }
                            if (isset($Operation['parameters']['mail']['on_error']['mail_to'])) {
                                array_push($Note, 'Mail on error to '.$Operation['parameters']['mail']['on_error']['mail_to']);
                            }
                            if (!empty($Note)) {
                                $uml .= "note right\n".implode("\n",$Note)."\nend note\n";
                            }
                            $n++;
                        }
                    }
                }
                $uml .= "@enduml";     

                $plantuml = $this->container->get('arii_core.plantuml');
                return $plantuml->graph($uml,[ 'output' => 'png']);
                break;
            case 'dhtmlx':
                $type = 'xml';
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Operations,'alarmTime,alarm,jobName,stateGrid,status,theUser,eventComment,nb,stateTime,state,firstTime,description','state');        
                break;
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Operations, 'xml', SerializationContext::create()->setGroups(array('default')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Operations, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }
   
}