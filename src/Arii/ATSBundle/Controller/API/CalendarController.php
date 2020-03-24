<?php

namespace Arii\ATSBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class CalendarController extends Controller
{

    public function daysAction($instanceId='ACE', $calendarName, $outputFormat, Request $request )
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);

        # On retouve le bon repo
        $state = $this->container->get('arii_ats.jobdb'); 
        $repoId = $state->getRepo($instanceId);
        $em = $this->getDoctrine()->getManager($repoId); 
        
        // on verifie les dates
        foreach (['minDate','maxDate'] as $date) {
            if (isset($Filter[$date]))
                $Filter[$date] = new \DateTime($Filter[$date]);
        }
        $Days = $state->calendarDays($em,$calendarName,$Filter);
        
        $response = new Response();
        if (empty($Days)) {
            // Si il n'y a pas de date, on teste si le calendrier existe
            $Filter['calendarName'] = $calendarName;
            $Cal = $em->getRepository("AriiATSBundle:UjoCalendar")->findCalendars($Filter);
            if ($Cal) {
                $response->setStatusCode(Response::HTTP_NO_CONTENT);
            }
            else {
                $response->setStatusCode(Response::HTTP_NOT_FOUND);
                $Days = [
                    'code' =>  'HTTP_NOT_FOUND',
                    'message' => 'This calendar does not exist.',
                    'display' => $this->get('translator')->trans('ATSAPI001', ['calendarName'=>$calendarName], 'api', $Accept['language'])
                ];
            }
        }
        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            default:
                $data = $this->get('jms_serializer')->serialize($Days, 'json');
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        $response->setContent($data);
        return $response;
    }

    public function jobsAction($instanceId='ACE', $calendarName, $outputFormat, Request $request )
    {
        $http = $this->container->get('arii_core.http');    
        $Accept = $http->Accept($request);
        $Filter = $http->Filter($request);
        
        # On retouve le bon repo
        $state = $this->container->get('arii_ats.jobdb'); 
        $repoId = $state->getRepo($instanceId);
        $em = $this->getDoctrine()->getManager($repoId);             
        $Jobs = $state->calendarJobs($em,$calendarName,$Filter);

        switch ($outputFormat==''?$Accept['outputFormat']:substr($outputFormat,1)) {
            default:
                $data = $this->get('jms_serializer')->serialize($Jobs, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        return $response;
    }
    
}