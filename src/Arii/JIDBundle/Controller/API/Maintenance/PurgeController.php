<?php

namespace Arii\JIDBundle\Controller\API\Maintenance;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use JMS\Serializer\SerializationContext;

class PurgeController extends Controller
{
    // peut on calculer la limite ?
    // La limite doit être supérieure au nombre d'execution dans une periode d'appel du web service
    
    /* La procédure permet de nettoyer les steps sans execution,
     * Ce cas intervient dans le cas d'une erreur Oracle (compte locké par exemple)
    */
    public function stepsAction($repoId='ojs_db',$retention=15)            
    {
        set_time_limit(600);
        
        $Filters = $this->container->get('arii.filter')->getRequestFilter();
        $em = $this->getDoctrine()->getManager($repoId);
        $Steps = $em->getRepository("AriiJIDBundle:SchedulerOrderStepHistory")->findStepsWithoutHistory(1000);
        
        $count=0;
        $deletedSteps = [];
        foreach($Steps as $Step) {
            $History = $Step->getHistory();
            $id = $Step->getTask()->getId();
            $deletedSteps[$id] = [
                'Step'      => $Step->getStep(),
                'StartTime' => $Step->getStartTime(),
                'EndTime'   => $Step->getEndTime(),
                'Error'     => $Step->getErrorText()
            ];
            $count++;
            $em->remove($Step);   
        }
        $Status = [ 
            "count"  => $count,
            "Steps" => $deletedSteps
        ];
        $data = $this->get('jms_serializer')->serialize($Status, 'json', SerializationContext::create()->setGroups(array('list')));
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        $em->flush();
            
        return $response;
    }
    
    // Tourne en boucle sur Oracle !
    public function ordersAction($repoId='ojs_db',$retention=15)            
    {
        set_time_limit(600);
        
        $Filters = $this->container->get('arii.filter')->getRequestFilter();
        $em = $this->getDoctrine()->getManager($repoId);
        $Orders = $em->getRepository("AriiJIDBundle:SchedulerOrderHistory")->findOrdersWithoutSteps(100);
        
        $count=0;
        $deletedOrders = [];
        foreach($Orders as $Order) {
            $id = $Order->getHistory();
            $deletedOrders[$id] = [
                'orderId'   => $Order->getOrderId(),
                'jobChain'  => $Order->getJobChain(),
                'spoolerId' => $Order->getSpoolerId(),
                'title'     => $Order->getTitle(),
                'state'     => $Order->getState(), 
                'stateText' => $Order->getStateText(),
                'startTime' => $Order->getStartTime(),
                'endTime'   => $Order->getEndTime()
            ];
            $count++;
            $em->remove($Order);   
        }
        $Status = [ 
            "count"  => $count,
            "Orders" => $deletedOrders
        ];
        $data = $this->get('jms_serializer')->serialize($Status, 'json', SerializationContext::create()->setGroups(array('list')));
        $response = new Response($data);
        $response->headers->set('Content-Type', 'application/json');
        $em->flush();
            
        return $response;
    }
}