<?php
// src/Arii/JIDBundle/Service/AriiStatus.php
/*
 * Sous-couche appelée par les API et les contrôleurs
 * Recupere les infos sous forme de tableau
 * Full ORM
 */
namespace Arii\JIDBundle\Service;

class AriiStatus
{
    protected $portal;
    protected $tools;
    protected $TZoffset;
    protected $TZinterval;
    
    public function __construct (
            \Arii\CoreBundle\Service\AriiPortal $portal,
            \Arii\CoreBundle\Service\AriiTools $tools ) {

        $this->portal = $portal;
        $this->tools = $tools;        
        
        # Recalage Doctrine
        $timezone = new \DateTimeZone(date_default_timezone_get());
        $this->TZoffset = $timezone->getOffset(new \DateTime()); 
    }

 /*********************************************************************
 * LastErrors
 *********************************************************************/
    public function LastErrors($em) {

        // On se base sur l'historique
        $Status = $em->getRepository("AriiJIDBundle:SchedulerOrderHistory")->findOrders();
        $Errors = [];
        $Done = [];
        foreach ($Status as $k=>$Error) {    
            $id = $Error['spoolerId'].'#'.$Error['jobChain'].'#'.$Error['orderId'].'#'.$Error['state'];
            if (isset($Done[$id]))
                continue;
            $Done[$id]=1;
            if (substr($Error['state'],0,1)!='!')
                continue;
            
            $Errors[$k] = $Error;
            $Errors[$k]['color'] = 'FAILURE';
            $Errors[$k]['message'] = '';
            if ($Error['stateText'] != '') 
                $Errors[$k]['message'] .= $Error['stateText'].' ';
            if ($Error['title'] != '') 
                $Errors[$k]['message'] .= '('.$Error['title'].')';
        }
        return $Errors;
    }

    
 /*********************************************************************
 * Orders
 *********************************************************************/
    public function SuspendedOrders($em) {

        // On se base sur l'historique
        $Status = $em->getRepository("AriiJIDBundle:SchedulerOrders")->findSuspendedOrders();
        $Orders = [];
        foreach ($Status as $k=>$Order) {    
            $Orders[$k] = $Order;
            $Orders[$k]['color'] = 'FAILURE';
        }
        return $Orders;
    }

 /*********************************************************************
 * Chains
 *********************************************************************/
    public function StoppedChains($em) {

        // On se base sur l'historique
        $Status = $em->getRepository("AriiJIDBundle:SchedulerJobChains")->findStoppedChains();
        $Chains = [];
        foreach ($Status as $k=>$Chain) {    
            $Chains[$k] = $Chain;
            $Chains[$k]['color'] = 'FAILURE';
        }
        return $Chains;
    }

 /*********************************************************************
 * Jobs
 *********************************************************************/
    public function StoppedJobs($em) {

        // On se base sur l'historique
        $Status = $em->getRepository("AriiJIDBundle:SchedulerJobs")->findStoppedJobs();
        $Jobs = [];
        foreach ($Status as $k=>$Job) {    
            $Jobs[$k] = $Job;
            $Jobs[$k]['color'] = 'FAILURE';
        }
        return $Jobs;
    }

 /*********************************************************************
 * Nodes
 *********************************************************************/
    public function StoppedNodes($em) {

        // On se base sur l'historique
        $Status = $em->getRepository("AriiJIDBundle:SchedulerJobChainNodes")->findStoppedNodes();
        $Nodes = [];
        foreach ($Status as $k=>$Node) {    
            $Nodes[$k] = $Node;
            if ($Node['action']=='next_state') {
                $Nodes[$k]['action'] = 'SKIPPED';
                $Nodes[$k]['color'] = 'WARNING';
            }
            elseif ($Node['action']=='stop') {
                $Nodes[$k]['action'] = 'STOPPED';
                $Nodes[$k]['color'] = 'FAILURE';
            }
        }
        return $Nodes;
    }

    public function SkippedNodes($em) {

        // On se base sur l'historique
        $Status = $em->getRepository("AriiJIDBundle:SchedulerJobChainNodes")->findSkippedNodes();
        $Nodes = [];
        foreach ($Status as $k=>$Node) {    
            $Nodes[$k] = $Node;
            if ($Node['action']=='next_state') {
                $Nodes[$k]['action'] = 'SKIPPED';
                $Nodes[$k]['color'] = 'WARNING';
            }
            elseif ($Node['action']=='stop') {
                $Nodes[$k]['action'] = 'STOPPED';
                $Nodes[$k]['color'] = 'FAILURE';
            }
        }
        return $Nodes;
    }
    
}

