<?php

namespace Arii\ATSBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class AuditController extends Controller
{

    public function listAction($instanceId='ACE', $outputFormat, Request $request )
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
        // on verifie les dates
        foreach (['minDate','maxDate'] as $date) {
            if (isset($Filter[$date]))
                $Filter[$date] = new \DateTime($Filter[$date]);
        }
    
        $state = $this->container->get('arii_ats.state');
        $repoId = $state->getRepo($instanceId);
        if (empty($repoId)) {
            $response = new Response($this->get('jms_serializer')->serialize(
                [
                    'code' =>  'HTTP_NOT_FOUND',
                    'message' => 'This instance does not exist.',
                    'display' => $this->get('translator')->trans('ATSAPI000', ['instanceName'=>$instanceId], 'api', $Accept['language'])
                ], 'json' ));
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            return $response;
        } 
        
        $em = $this->getDoctrine()->getManager($repoId);             
        $Audit = $state->Audit($em,$Filter);
        if (empty($Audit)) {
            $response = new Response();
            $response->setStatusCode(Response::HTTP_NO_CONTENT);
            return $response;
        } 
        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            default:
                $data = $this->get('jms_serializer')->serialize($Audit, 'json');
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }

    public function getAction($instanceId='ACE', $auditId, $outputFormat, Request $request )
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
    
        $state = $this->container->get('arii_ats.state');
        $repoId = $state->getRepo($instanceId);
        if (empty($repoId)) {
            $response = new Response($this->get('jms_serializer')->serialize(
                [
                    'code' =>  'HTTP_NOT_FOUND',
                    'message' => 'This instance does not exist.',
                    'display' => $this->get('translator')->trans('ATSAPI000', ['instanceName'=>$instanceId], 'api', $Accept['language'])
                ], 'json' ));
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            return $response;
        } 
        
        $em = $this->getDoctrine()->getManager($repoId);             
        $Audit = $state->AuditMsg($em,$auditId,$Filter);
        if (empty($Audit)) {
            $response = new Response($this->get('jms_serializer')->serialize(
                [
                    'code' =>  'HTTP_NOT_FOUND',
                    'message' => 'This Id does not exist.'
                ], 'json' ));
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            return $response;
        } 
        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            default:
                $data = $this->get('jms_serializer')->serialize($Audit, 'json');
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }
    
    public function jobsAction($instanceId='ACE', $outputFormat, Request $request)
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
    
        $state = $this->container->get('arii_ats.state');
        $repoId = $state->getRepo($instanceId);
        $em = $this->getDoctrine()->getManager($repoId);             
        $Changes = $state->AuditJobs($em,$Filter);

        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            case 'dhtmlxGrid':
                $dhtmlx = $this->container->get('arii_core.render'); 
                return $dhtmlx->grid($Changes,'date,event,job,user,host','event');        
                break;
            case 'xml':
                $data = $this->get('jms_serializer')->serialize($Changes, 'xml', SerializationContext::create()->setGroups(array('default')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/xml');
                break;
            default:
                $data = $this->get('jms_serializer')->serialize($Changes, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }


}