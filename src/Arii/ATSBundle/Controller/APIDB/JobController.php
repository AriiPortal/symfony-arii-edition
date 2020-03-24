<?php

namespace Arii\ATSBundle\Controller\APIDB;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class JobController extends Controller
{

    public function statusAction($repoId='ats_db', $jobId, $outputFormat, Request $request )
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
    
        $em = $this->getDoctrine()->getManager($repoId);        
        $state = $this->container->get('arii_ats.job');   
        $Status = $state->Status($em,$jobId,$Filter);

        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            default:
                $data = $this->get('jms_serializer')->serialize($Status, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }
    
    public function runsAction($repoId='ats_db',$jobId,$outputFormat, Request $request )
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
        
        $em = $this->getDoctrine()->getManager($repoId);        
        $state = $this->container->get('arii_ats.job');        
        $Runs = $state->Runs($em,$jobId,$Filter);

        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
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
            default:
                $data = $this->get('jms_serializer')->serialize($Runs, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }
    
    public function graphsAction($repoId='ats_db',$jobId,$outputFormat )
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();
        
        // pour les urls sans accept
        if ($outputFormat!='')
            $Filters['outputFormat'] = substr($outputFormat,1);

        $Graphs = [
            [
                "id" => 1,
                "name" => "StatusByPeriod",
                "description" => "Give a status for a period of time"
            ]
        ];
        
        switch ($Filters['outputFormat']) {
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Graphs, 'xml', SerializationContext::create()->setGroups(array('default')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Graphs, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }
    
    public function generateAction($repoId='ats_db',$jobId,$graphId,$outputFormat )
    {
        $Filters = $this->container->get('arii.filter')->getRequestFilter();
        
        // pour les urls sans accept
        if ($outputFormat!='')
            $Filters['outputFormat'] = substr($outputFormat,1);

        $em = $this->getDoctrine()->getManager($repoId);        
        $state = $this->container->get('arii_ats.state');        
        $jobId = $state->JobIdByName($em,$jobId);

        if (!isset($jobId))
            return;
        $Runs = $state->JobRuns($em,$jobId,$Filters);

        // Specifique au graphique
        $Request = Request::createFromGlobals();
        $period = ($Request->query->get('period')>0?$Request->query->get('period'):6);
        $height = ($Request->query->get('height')>0?$Request->query->get('height'):24);
        $col    = ($Request->query->get('col_width')>0?$Request->query->get('col_width'):4);

        $Data = [];
        // Ajout de offset 
        foreach ($Runs as $Run) {
            array_push($Data,
                array(  'startTime' => $Run['starttime'],
                        'DateTime'  => $Run['startTime'],
                        'runTime'   => $Run['runtime'],
                        'success'   => $Run['success']
                    )
            );
        }        
        switch ($Filters['outputFormat']) {
            case 'png':
                $gd = $this->container->get('arii_core.gd');
                switch (strtolower($graphId)) {
                    case '1':
                    case 'statusbyperiod':
                        $img = $gd->StatusByPeriod($Data,$period,$height,$col);
                        break;
                    default:
                        $img = $gd->StatusByPeriod($Data,$period,$height,$col);
                        break;
                }
                $response = new Response();
                $response->headers->set('Content-Type', 'image/png');
                $response->sendHeaders();
                imagepng($img);
                imagedestroy($img);
                break;
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Data, 'xml', SerializationContext::create()->setGroups(array('default')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Data, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }
    
}