<?php
namespace Arii\JAPIBundle\Service;

// Get information from array
class AriiConvert
{
    protected $tools;
    
    public function __construct( \Arii\CoreBundle\Service\AriiTools $tools ) {
        $this->tools = $tools;
    }

    function Chain($Info) {
        $Nodes = [];
        foreach ($Info['job_chain_node'] as $Node) {
            array_push($Nodes, $this->Node($Node));
        }        
        $Chain = [
                'name' =>  $Info['attr']['name'],
                'path' =>  $Info['attr']['path'],                
                'state' =>  $Info['attr']['state'],
                'title' =>  (isset($Info['attr']['title'])?$Info['attr']['title']:null),
                'orders' =>  (int) $Info['attr']['orders'],
                'runningInfos' =>  (isset($Info['attr']['running_orders'])?(int) $Info['attr']['running_orders']:null),
                'isInfosRecoverable' => (isset($Info['attr']['is_orders_recoverable']) and ($Info['attr']['is_orders_recoverable']=='yes'?true:false)),
                'nodes' => $Nodes
        ];
        return $Chain;
    }
    
    function Node($Info) {   
        $Orders = [];
        if (isset($Info['order_queue']['order'])) {
            if (is_array($Info['order_queue']['order'])) 
                foreach ($Info['order_queue']['order'] as $Order) {
                    array_push($Orders, $this->Order($Order));
                } 
            else 
                array_push($Orders, $this->Order($Order));
        }
        
        $Node = [
            'state' => $Info['attr']['state'],
            'nextState' => (isset($Info['attr']['next_state'])?$Info['attr']['next_state']:null),
            'errorState' => (isset($Info['attr']['error_state'])?$Info['attr']['error_state']:null),
            'job' => (isset($Info['attr']['job'])?$Info['attr']['job']:null),
            'orders' => $Orders
        ];
        return $Node;
    }

    function Order($Info) {   
        $chainPath = dirname($Info['attr']['job_chain']);
        $chainName = basename($Info['attr']['job_chain']);
        $jobPath = dirname($Info['attr']['job']);
        $Order = [
            'name' =>  $Info['attr']['order'],
            'path' =>  dirname($Info['attr']['job_chain']),
            'state' => $Info['attr']['state'],
            'initialState' => $Info['attr']['initial_state'],
            'title' => (isset($Info['attr']['title'])?$Info['attr']['title']:null),
            'stateText' => (isset($Info['attr']['state_text'])?$Info['attr']['state_text']:null),
            'isSuspended' => $isSuspended = (isset($Info['attr']['suspended']) and ($Info['attr']['suspended']=='yes')?true:false),
            'startDate' =>  (isset($Info['attr']['start_time'])?$Info['attr']['start_time']:null),
            'nextStartDate' => (isset($Info['attr']['next_start_time'])?$Info['attr']['next_start_time']:null),
            'activeSchedule' => (isset($Info['attr']['active_schedule'])?$Info['attr']['active_schedule']:null),
            'orderId' =>  $chainName.','.$Info['attr']['id'],
            'chainName' => $chainName,
            'jobName' => basename($Info['attr']['job']),
            'jobPath' => ($jobPath!=$chainPath?$jobPath:null),
            'creationDate' => $Info['attr']['created'],
            'priority' =>  (int) $Info['attr']['priority'],
            'historyId' =>  (int) (isset($Info['attr']['history_id'])?$Info['attr']['history_id']:null),
            'isTouched' =>  (isset($Info['attr']['touched']) and ($Info['attr']['touched']=='yes')?true:false),
            'isNestedTouched' =>  (isset($Info['attr']['nested_touched']) and ($Info['attr']['nested_touched']=='yes')?true:false),
            'logFile' => (isset($Info['attr']['logFile'])?$Info['attr']['logFile']:null)
        ];
        return $Order;
    }
}
