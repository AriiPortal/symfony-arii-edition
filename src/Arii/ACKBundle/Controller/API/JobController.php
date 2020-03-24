<?php

namespace Arii\ACKBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class JobController extends Controller
{        
    public function instructionsAction($jobName, $outputFormat, Request $request )
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
        
        # A passer en service ?
        # Optimisation en passant l'application ?
        $Alerts = $this->getDoctrine()->getRepository("AriiACKBundle:Alert")->Alerts();      
        $Check = array();
        $Result = [];
        $len_job = strlen($jobName);
        foreach ($Alerts as $Alert) {
            $pattern = $Alert->getPattern();
            // supprime les consignes trop génériques
            if (($pattern == '.*') or ($pattern == '^.'))
                continue;
                       
            $pstatus = $Alert->getStatus();
            $pexit = $Alert->getExitCodes();            
            if (!preg_match('/'.$pattern.'/',$jobName,$matches)) 
                continue;
            
            $id = $pstatus.'#'.$pexit;
            if (!isset($max[$id]))
                $max[$id]=0;
            # Le score est la taille du pattern le plus grand
            # Pour l'id en cours
            $score = strlen($pattern);
            if ($score>=$max[$id]) {                
                $max[$id]=$score;
                $len_pattern = strlen(str_replace(['^','$','\\','[',']'],['','','','',''],$pattern));
                $percent = round($len_pattern*100/$len_job);
                // On conserve chaque type d'alerte           
                $Result[$id] = array(
                    'pattern'     => $pattern,
                    'status'      => $pstatus,
                    'exit_code'   => $pexit,
                    'name'        => $Alert->getName(),
                    'to_do'       => $Alert->getToDo(),
                    'description' => $Alert->getDescription(),
                    'message'     => $Alert->getMessage(),
                    'msg_from'    => $Alert->getMsgFrom(),                
                    'msg_to'      => $Alert->getMsgTo(),
                    'msg_cc'      => $Alert->getMsgCc(),
                    'score'       => $score,
                    'percent'     => $percent
                );
            }
        }     

        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            default:
                $data = $this->get('jms_serializer')->serialize(array_values($Result), 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }
   
}