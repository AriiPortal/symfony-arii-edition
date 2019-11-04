<?php
// src/Arii/JIDBundle/Service/AriiAPI.php
/*
 * Sous-couche appelée par les API et les contrôleurs
 * Recupere les infos sous forme de tableau
 * Full ORM
 */
namespace Arii\JIDBundle\Service;

class AriiScheduler
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
 * Nodes
 *********************************************************************/
    public function Nodes($em,$Filter) {        
        // On se base sur l'historique
        $Jobs = $em->getRepository("AriiJIDBundle:SchedulerJobChainNodes")->findNodes($Filter);
        foreach ($Jobs as $k=>$Job) {
            $Jobs[$k]['jobChain'] = '/'.$Jobs[$k]['jobChain'];
            if ($Job['clusterMemberId']=='-') {
                unset($Jobs[$k]['clusterMemberId']);
            }
            $Jobs[$k]['isSkipped'] = ($Job['action']=='next_state'?1:0);
            $Jobs[$k]['isStopped'] = ($Job['action']=='stop'?1:0);
        }
        return $Jobs;
    }
    
 /*********************************************************************
 * Jobs
 *********************************************************************/
    public function Jobs($em,$Filter) {        
        // On se base sur l'historique
        $Jobs = $em->getRepository("AriiJIDBundle:SchedulerJobs")->findJobs($Filter);
        foreach ($Jobs as $k=>$Job) {
            $Jobs[$k]['jobName'] = '/'.$Jobs[$k]['jobName'];
            if ($Job['clusterMemberId']=='-') {
                unset($Jobs[$k]['clusterMemberId']);
            }
        }
        return $Jobs;
    }

 /*********************************************************************
 * Tasks
 *********************************************************************/
    public function Tasks($em,$Filter=[]) {        
        // On se base sur l'historique
        $Tasks = $em->getRepository("AriiJIDBundle:SchedulerTasks")->findTasks($Filter);
        return $Tasks;
    }
    
 /*********************************************************************
 * Chains
 *********************************************************************/
    public function Chains($em,$Filter=[]) {        
        // On se base sur l'historique
        $Chains = $em->getRepository("AriiJIDBundle:SchedulerJobChains")->findChains($Filter);
        return $Chains;
    }

 /*********************************************************************
 * Orders
 *********************************************************************/
    public function Orders($em, $Filter) {        
        // On se base sur l'historique
        $Orders = $em->getRepository("AriiJIDBundle:SchedulerOrders")->findOrders($Filter);
        foreach ($Orders as $k=>$Order) {
            // Conversion des xml
            foreach(['orderXml','runTime','payload'] as $f) {
                if (isset($Order[$f])) {
                    $xml = simplexml_load_string($Order[$f]);
                    $json = json_encode($xml);
                    $Orders[$k][$f] = json_decode($json,TRUE);
                }
            }
        }
        return $Orders;
    }

    public function Order($em,$spoolerId,$jobChain,$orderId) {

        // On se base sur l'historique
        $Orders = $em->getRepository("AriiJIDBundle:SchedulerOrders")->findOrder($spoolerId,$jobChain,$orderId);
        if (!$Orders) return [];
        $Order = array_pop($Orders);
        return $this->getOrder($Order);
    }

    private function getOrder ($Order) {
        if ($Order['createdTime'])
            $Order['createdTime']->modify($this->TZoffset." second");
        
        if ($Order['modTime'])
            $Order['modTime']->modify($this->TZoffset." second");

        $order_xml = $this->tools->xml2array($Order['orderXml']);
        $setback = 0; $setback_time = '';
        $order_status = 'WAIT';
        $next_time = '';
        if (isset($order_xml['order_attr']['start_time'])) {
            $next_time = $order_xml['order_attr']['start_time'];
            // $next_time->modify($this->TZoffset." second");
        }
        $at = '';
        if (isset($order_xml['order_attr']['at'])) {
            $order_status = 'PLANNED';
            $at = new \DateTime($order_xml['order_attr']['at']);
            $at->modify($this->TZoffset." second");
        }
        if (isset($order_xml['order_attr']['suspended']) && $order_xml['order_attr']['suspended'] == "yes")
        {
            $order_status = "SUSPENDED";
        }
        elseif (isset($order_xml['order_attr']['setback_count']))
        {
            $order_status = "SETBACK";
            $setback = $order_xml['order_attr']['setback_count'];
            $setback_time = $order_xml['order_attr']['setback'];
        }
        $hid = 0;
        if (isset($order_xml['order_attr']['history_id'])) {
            $hid = $order_xml['order_attr']['history_id'];
        }
        
        $log = '';
        if (isset($order_xml['order']['log']))
            $log = $order_xml['order']['log'];
        $Order['status'] = $order_status;
        $Order['nextTime'] = $next_time;
        $Order['history'] = $hid;
        $Order['at'] = $at;
        $Order['orderId'] = $Order['id'];
        $Order['log'] = $log;
        return $Order;
    }
    
}

