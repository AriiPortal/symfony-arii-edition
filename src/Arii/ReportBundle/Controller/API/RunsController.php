<?php

namespace Arii\ReportBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class RunsController extends Controller
{
    public function getAction($outputFormat, Request $request)
    {
        $em = $this->getDoctrine()->getManager();        
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);

        $state = $this->container->get('arii_report.Runs');
        $Runs = $state->Runs($em,$Filter);

        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            case 'png':
                // Preparation des datas
                /* $Data = [ 
                    "startTime"  => 1565190002,
                    "runTime" => 1,
                    "success" => 0 ],
                 */
                
                $Data=[];
                // seulement 100
                foreach ($Runs as $k=>$Run) {
                    $startTime = $Run['start_time']->format('U');
                    $endTime = $Run['end_time']->format('U');
                    $runTime = $endTime - $startTime;
                    array_push($Data, [
                        "startTime"  => $startTime,
                        "runTime" => $runTime,
                        "success" => ($Run['status']=='SUCCESS'?1:0)
                    ] );
                }
                $gd = $this->container->get('arii_core.gd');
                $img = $gd->StatusByPeriod($Data,24,24,5);
                $response = new Response();
                $response->headers->set('Content-Type', 'image/png');                
                $response->sendHeaders();   
                imagepng($img);
                imagedestroy($img);
                break;
            case 'dhtmlx':
                $type = 'xml';
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Runs,'alarmTime,alarm,jobName,stateGrid,status,theUser,eventComment,nb,stateTime,state,firstTime,description','state');        
                break;
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Runs, 'xml', SerializationContext::create()->setGroups(array('default')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            case 'csv':
                $output = $this->container->get('arii_core.output');
                $response = new Response($output->Csv($Runs));
                $response->headers->set('Content-Type', 'text/plain');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Runs, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }

}