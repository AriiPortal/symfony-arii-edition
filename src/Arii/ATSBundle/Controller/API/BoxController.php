<?php

namespace Arii\ATSBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class BoxController extends Controller
{

    public function jobsAction($instanceId='ACE', $jobName, $outputFormat, Request $request )
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);       
        # Completons les parametres
        if (!isset($Filter['depth']))
            $Filter['depth']=0;
        if (!isset($Filter['dependencies']))
            $Filter['dependencies']=1;
        
        # On retouve le bon repo
        $state = $this->container->get('arii_ats.jobdb'); 
        $repoId = $state->getRepo($instanceId);
        $em = $this->getDoctrine()->getManager($repoId);
        $Children = $state->Children($em,$jobName,$Filter);

        $outFormat = $outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1);
        switch ($outFormat) {
            case 'cmap':
            case 'dot':
            case 'png':
            case 'svg':
                $graphviz = $this->container->get('arii_ats.graphviz');
                $Options = array(
                    'digraph' => false,
                    'output' => $outFormat
                );
                return $graphviz->Jobs($Children,$Options);                
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Children, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }
    
    public function statusAction($instanceId='ACE', $jobName, $outputFormat, Request $request )
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
        
        # On retouve le bon repo
        $state = $this->container->get('arii_ats.jobdb'); 
        $repoId = $state->getRepo($instanceId);
        $em = $this->getDoctrine()->getManager($repoId);             
        $Runs = $state->Status($em,$jobName,$Filter);

        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            default:
                $data = $this->get('jms_serializer')->serialize($Runs, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }

    public function detailAction($instanceId='ACE', $jobName, $outputFormat, Request $request )
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
    
        $client = $this->container->get('arii_ats.client');   
        $Filter['instanceId'] = $instanceId;
        $Filter['jobName'] = $jobName;        
        $Code = $client->Autorep_d($Filter);

        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            default:
                $response = new Response($Code);
                $response->headers->set('Content-Type', 'text/plain');
                break;                
        }
        return $response;
    }
    
    public function logsAction($instanceId='ACE', $jobName, $logType, $outputFormat, Request $request )
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
    
        $client = $this->container->get('arii_ats.client');  
        $Filter['instanceId'] = $instanceId;
        $Filter['jobName'] = $jobName;
        $Filter['logType'] = $logType;
        $Log = $client->Autosyslog($Filter);

        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            default:
                $response = new Response($Log);
                $response->headers->set('Content-Type', 'text/plain');
                break;                
        }
        return $response;
    }

    public function codeAction($instanceId='ACE', $jobName, $outputFormat, Request $request )
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
    
        $client = $this->container->get('arii_ats.client');   
        $Filter['instanceId'] = $instanceId;
        $Filter['jobName'] = $jobName;        
        $Code = $client->Autorep_q($Filter);

        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            default:
                $response = new Response($Code);
                $response->headers->set('Content-Type', 'text/plain');
                break;                
        }
        return $response;
    }

    public function reportAction($instanceId='ACE', $jobName, $outputFormat, Request $request )
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
    
        $client = $this->container->get('arii_ats.client');   
        $Filter['instanceId'] = $instanceId;
        $Filter['jobName'] = $jobName;
        $Report = $client->Autorep($Filter);

        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            default:
                $response = new Response($Report);
                $response->headers->set('Content-Type', 'text/plain');
                break;                
        }
        return $response;
    }

    public function auditAction($instanceId='ACE', $jobName, $outputFormat, Request $request )
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
    
        $client = $this->container->get('arii_ats.client');   
        $Filter['instanceId'] = $instanceId;
        $Filter['jobName'] = $jobName;
        $Audit = $client->Autotrack($Filter);

        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            default:
                $response = new Response($Audit);
                $response->headers->set('Content-Type', 'text/plain');
                break;                
        }
        return $response;
    }

    public function runsAction($instanceId='ACE',$jobName,$outputFormat, Request $request )
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
        
        # On retouve le bon repo
        $state = $this->container->get('arii_ats.jobdb'); 
        $repoId = $state->getRepo($instanceId);
        $em = $this->getDoctrine()->getManager($repoId);             
        $Runs = $state->Runs($em,$jobName,$Filter);        
        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            case 'png':
                $gd = $this->container->get('arii_core.gd');
                $img = $gd->StatusByPeriod($Runs,24,24,5);
                $response = new Response();
                $response->headers->set('Content-Type', 'image/png');                
                $response->sendHeaders();   
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Runs, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }
    
    public function runtimesAction($instanceId='ACE',$jobName,$outputFormat, Request $request )
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
        
        # On retouve le bon repo
        $state = $this->container->get('arii_ats.jobdb'); 
        $repoId = $state->getRepo($instanceId);
        $em = $this->getDoctrine()->getManager($repoId);             
        $Runs = $state->Runtimes($em,$jobName,$Filter);
        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            case 'png':
                $gd = $this->container->get('arii_core.gd');
                $img = $gd->runtimes($Runs,200,30,5);
                $response = new Response();
                $response->headers->set('Content-Type', 'image/png');                
                $response->sendHeaders();   
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