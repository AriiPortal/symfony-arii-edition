<?php

namespace Arii\JIDBundle\Controller\API\Maintenance;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class ArchiveController extends Controller
{
    // peut on calculer la limite ?
    // La limite doit être supérieure au nombre d'execution dans une periode d'appel du web service

    // version avec jointure (plus lente mais qui marche)
    public function ordersAction($repoId='ojs_db',$retention=15,$limit=1000)
    {
        set_time_limit(900);

        $Filters = $this->container->get('arii.filter')->getRequestFilter();

        $em = $this->getDoctrine()->getManager($repoId);
        $maxtime = new \DateTime();
        $maxtime->modify('-'.$retention.' days');        
        $LatestOrders = $em->getRepository("AriiJIDBundle:SchedulerOrderHistory")->findLatestOrders($maxtime,1);
                
        // Ouverture du fichier dans le workspace
        $portal = $this->container->get('arii_core.portal');
        $now = new \DateTime();
                
        $count = 0;
        $deletedOrders = [];
        foreach ($LatestOrders as $Order) {
            $count++;
            $historyId = $Order['history'];
            
            $Steps = $em->getRepository("AriiJIDBundle:SchedulerOrderStepHistory")->findByOrder($historyId);
            $deletedSteps = [];
            foreach ($Steps as $Step) {
                $taskId = $Step['taskId'];
                
                $deletedSteps[$taskId] = [
                    'historyId' => $historyId,
                    'jobName'   => $Step['jobName'],

                    'step'      => $Step['step'],
                    'state'     => $Step['state'], 
                    'startTime' => $Step['startTime'],
                    'endTime'   => $Step['endTime'],
                    'error'     => $Step['error'],
                    'errorCode' => $Step['errorCode'],
                    'errorText' => $Step['errorText'],

                    'clusterMemberId' => $Step['clusterMemberId'],
                    'steps'           => $Step['steps'],
                    'exitCode'        => $Step['exitCode']
                ];

                $Reftask = $em->getRepository('AriiJIDBundle:SchedulerHistory')->find($taskId);
                if ($Reftask) {
                    $em->remove($Reftask);
                    $em->flush();
                }
                
                $RefStep = $em->getRepository('AriiJIDBundle:SchedulerOrderStepHistory')->find($taskId);
                if ($RefStep) {
                    $em->remove($RefStep);
                    $em->flush();
                }
                
            }
            $deletedOrders[$historyId] = [
                'orderId'   => $Order['orderId'],
                'jobChain'  => $Order['jobChain'],
                'spoolerId' => $Order['spoolerId'],
                'title'     => $Order['title'],
                'state'     => $Order['state'], 
                'stateText' => $Order['stateText'],
                'startTime' => $Order['startTime'],
                'endTime'   => $Order['endTime'],
                'Steps'     => $deletedSteps
            ];  
            
        }
        
        $RefOrder = $em->getRepository("AriiJIDBundle:SchedulerOrderHistory")->find($historyId);
        $em->remove($RefOrder);
        
        $em->flush();
                    
        $Status = [
            "count" => $count,
            "Orders" => $deletedOrders
        ];
        
        switch ($Filters['outputFormat']) {
            default:
                $data = $this->get('jms_serializer')->serialize($Status, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        
        return $response;
    }
    
    // version ORM, bloque si l'historique est vide !
    public function orders2Action($repoId='ojs_db',$retention=15,$limit=1000)            
    {
        set_time_limit(900);

        $Filters = $this->container->get('arii.filter')->getRequestFilter();

        $em = $this->getDoctrine()->getManager($repoId);
        $maxtime = new \DateTime();
        $maxtime->modify('-'.$retention.' days');        
        $LatestOrders = $em->getRepository("AriiJIDBundle:SchedulerOrderHistory")->findLatestOrdersOLD($maxtime,1000);
                
        // Ouverture du fichier dans le workspace
        $portal = $this->container->get('arii_core.portal');
        $now = new \DateTime();
        
        $count = 0;
        $deletedOrders = [];
        foreach ($LatestOrders as $Order) {
            $count++;
            $historyId = $Order->getHistory();
            
            $Steps = $em->getRepository("AriiJIDBundle:SchedulerOrderStepHistory")->findBy([ 'history' => $historyId ]);
            $deletedSteps = [];
            foreach ($Steps as $Step) {                  
                $Task = $Step->getTask();
                $taskId = $Task->getId();
                $deletedSteps[$taskId] = [
                    'historyId' => $historyId,
                    'jobName'   => $Task->getJobName(),

                    'step'      => $Step->getStep(),
                    'state'     => $Step->getState(), 
                    'startTime' => $Step->getStartTime(),
                    'endTime'   => $Step->getEndTime(),
                    'error'     => $Step->getError(),
                    'errorCode' => $Step->getErrorCode(),
                    'errorText' => $Step->getErrorText(),

                    'clusterMemberId' => $Task->getClusterMemberId(),
                    'steps'           => $Task->getSteps(),
                    'exitCode'        => $Task->getExitCode()
                ];
                $em->remove($Task);
                $em->remove($Step); 
            }
            $deletedOrders[$historyId] = [
                'orderId'   => $Order->getOrderId(),
                'jobChain'  => $Order->getJobChain(),
                'spoolerId' => $Order->getSpoolerId(),
                'title'     => $Order->getTitle(),
                'state'     => $Order->getState(), 
                'stateText' => $Order->getStateText(),
                'startTime' => $Order->getStartTime(),
                'endTime'   => $Order->getEndTime(),
                'Steps'     => $deletedSteps
            ];  
            $em->remove($Order);            
        }
        
        $Status = [
            "count" => $count,
            "Orders" => $deletedOrders
        ];
        
        switch ($Filters['outputFormat']) {
            default:
                $data = $this->get('jms_serializer')->serialize($Status, 'json', SerializationContext::create()->setGroups(array('list')));
                $response = new Response($data);
                $response->headers->set('Content-Type', 'application/json');
                break;                
        }
        
        $em->flush();
            
        return $response;
    }
}