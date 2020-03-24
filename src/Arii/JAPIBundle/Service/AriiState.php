<?php
namespace Arii\JAPIBundle\Service;

class AriiState
{
    protected $tools;
    protected $convert;
    
    public function __construct(
            \Arii\CoreBundle\Service\AriiTools $tools,
            AriiConvert $convert ) {
        $this->tools = $tools;
        $this->convert = $convert;
    }

    public function get($instance='localhost:4444',$what='') {
        set_time_limit ( 900 );
        // pour l'instant, que du host:port
        if (($p=strpos($instance, ':'))>0)
            list($host,$port) = split(':',$instance);
        else 
            return;
        if ($what=='')
            $f= fopen("http://$host:$port/%3Cshow_state/%3E","r");
        else 
            $f= fopen("http://$host:$port/%3Cshow_state%20what=%22$what%22/%3E","r");

        if (!$f) {
            print "$spooler:$port !";
            exit();
        }
        $data = '';
        while(!feof($f)) {
            $data .= fread($f,10240);
        }
        fclose($f);
        return $this->tools->xml2array( $data , 1, 'attributes');
    }

    // Instance 
    function Instance($instance,$Filter) {
        $Data = $this->get($instance);
        if (!isset($Data['spooler']['answer']['state']))
            return []; 
        $Info = $Data['spooler']['answer']['state']['attr'];
        $Instance = [
            'spoolerName' => $Info['spooler_id'],
            'state' => $Info['state'],
            'hostName' => $Info['host'],
            'ipAddress' => $Info['ip_address'],
            'tcpPort' => $Info['tcp_port'],
            'udpPort' => $Info['udp_port'],
            'version' => $Info['version'],            
            'db' => $Info['db'],
            'configFile' => $Info['config_file'],
            'logFile' => $Info['log_file'],            
            'pid' => $Info['pid'],
            'loop' => $Info['loop'],
            'startDate' => $Info['spooler_running_since'],
            'timeZone' => $Info['time_zone'],
            'startDate' => $Info['spooler_running_since'],
            'waitUntil' => $Info['wait_until'],
            'waits' => $Info['waits']
        ];        
        if (isset($Data['spooler']['answer']['state']['cluster']['attr'])) {
            $Info = $Data['spooler']['answer']['state']['cluster']['attr'];
            $Instance['cluster'] = [
                'clusterMemberId' => $Info['cluster_member_id'],
                'isActive' => ($Info['active']=='yes'?true:false),
                'isAllowedToStart' => ($Info['is_member_allowed_to_start']=='yes'?true:false)
            ];
        }
/*        
        if (isset($Data['spooler']['answer']['state']['job_chains']['attr'])) {
            $Info = $Data['spooler']['answer']['state']['job_chains']['attr'];
            $Instance['job_chains'] = [
                'count' => $Info['count']
            ];
        }
 */
        return $Instance;
    }
    
    function Orders($instance,$Filter) {
        $Data = $this->get($instance,'job_orders');

        if (!isset($Data['spooler']['answer']['state']['jobs']['job']))
            return []; 
        $Orders = [];

        // Boucle de jobs ?
        if (isset($Data['spooler']['answer']['state']['jobs']['job']['attr'])) {
            $Jobs = [ $Data['spooler']['answer']['state']['jobs']['job'] ];     
        }
        else {
            $Jobs = $Data['spooler']['answer']['state']['jobs']['job'];
        }
        foreach($Jobs as $k=>$Job) {
            if (!isset($Job['order_queue'])) continue;
            if (!isset($Job['order_queue']['order'])) continue;

            // est ce qu'il n'y a qu'un ordre  ?
            if (isset($Job['order_queue']['order']['attr']))
                $Queue = [ $Job['order_queue']['order'] ];
            else
                $Queue = $Job['order_queue']['order'];

            // On traite le tableau d'ordres
            foreach ($Queue as $k => $O) {
                $Order = $O['attr'];
                if (!isset($Order['id'])) continue;

                // Filtres
                $id = $Order['id'];
                if (isset($Filter['orderName']) and ($Filter['orderName']!=$id)) continue;
                
                
                $isSuspended = (isset($Order['suspended']) and ($Order['suspended']=='yes')?true:false);
                if (isset($Filter['isSuspended']) and ($Filter['isSuspended']!=$isSuspended)) continue;
                
                if(isset($Order['start_time'])) {
                    if ($isSuspended)
                        $status = 'FAILURE';
                    else 
                        $status = 'RUNNING';
                }
                else if(isset($Order['next_start_time'])) {
                    $status = 'ACTIVATED';
                }
                else {
                    $status = 'INACTIVE';
                }                    
                if (isset($Filter['status']) and ($Filter['status']!=$status)) continue;
                
                $chainPath = dirname($Order['job_chain']);
                if (isset($Filter['path']) and ($Filter['path']!=$chainPath)) continue;
                $chainName = basename($Order['job_chain']);
                if (isset($Filter['chainName']) and ($Filter['chainName']!=$chainName)) continue;
                
                $jobPath = dirname($Order['job']);
                array_push($Orders, [
                    'name' =>  $Order['order'],
                    'path' =>  $chainPath,
                    'status' => $status,
                    'state' => $Order['state'],
                    'initialState' => $Order['initial_state'],
                    'title' => (isset($Order['title'])?$Order['title']:null),
                    'stateText' => (isset($Order['state_text'])?$Order['state_text']:null),
                    'isSuspended' => $isSuspended,
                    'startDate' =>  (isset($Order['start_time'])?$Order['start_time']:null),
                    'nextStartDate' => (isset($Order['next_start_time'])?$Order['next_start_time']:null),
                    'activeSchedule' => (isset($Order['active_schedule'])?$Order['active_schedule']:null),
                    'orderId' =>  $chainName.','.$Order['id'],
                    'chainName' => $chainName,
                    'jobName' => basename($Order['job']),
                    'jobPath' => ($jobPath!=$chainPath?$jobPath:null),
                    'creationDate' => $Order['created'],
                    'priority' =>  (int) $Order['priority'],
                    'historyId' =>  (int) (isset($Order['history_id'])?$Order['history_id']:null),
                    'isTouched' =>  (isset($Order['touched']) and ($Order['touched']=='yes')?true:false),
                    'isNestedTouched' =>  (isset($Order['nested_touched']) and ($Order['nested_touched']=='yes')?true:false),
                    'logFile' => (isset($Order['logFile'])?$Order['logFile']:null)
                ]);
            }
        }
        return $Orders;
    }
 
    function Chains($instance,$Filter) {
        $Data = $this->get($instance,'job_chains');
        if (!isset($Data['spooler']['answer']['state']['job_chains']['job_chain']))
            return []; 
        $Chains = [];
        foreach($Data['spooler']['answer']['state']['job_chains']['job_chain'] as $Chain) {
            array_push($Chains, [
                'name' =>  $Chain['attr']['name'],
                'path' =>  $Chain['attr']['path'],                
                'state' =>  $Chain['attr']['state'],
                'title' =>  (isset($Chain['attr']['title'])?$Chain['attr']['title']:null),
                'orders' =>  (int) $Chain['attr']['orders'],
                'runningOrders' =>  (int) $Chain['attr']['running_orders'],
                'isOrdersRecoverable' => (isset($Chain['attr']['isOrdersRecoverable']) and ($Chain['attr']['isOrdersRecoverable']=='yes'?true:false))                
            ]);
        }
        return $Chains;
    }

    function Nodes($instance,$chainId,$Filter) {
        $Data = $this->get($instance,'job_chains');
        if (!isset($Data['spooler']['answer']['state']['job_chains']['job_chain']))
            return []; 
        $Nodes = [];
        foreach($Data['spooler']['answer']['state']['job_chains']['job_chain'] as $Chain) {
            array_push($Chains, [
            ]);
        }
        return $Nodes;
    }
    
    // Get spooler from the supervisor
    function RemoteSchedulers($instance,$Filter) {
        $Data = $this->get($instance,'remote_schedulers');
        if (!isset($Data['spooler']['answer']['state']['remote_schedulers']['remote_scheduler']))
            return []; 
        $remoteSchedulers = [];
        foreach($Data['spooler']['answer']['state']['remote_schedulers']['remote_scheduler'] as $Remote) {
            array_push($remoteSchedulers, [
                'spoolerName' =>  $Remote['attr']['scheduler_id'],
                'hostName' => $Remote['attr']['hostname'],
                'hostPort' => (int) $Remote['attr']['udp_port'],
                'version' =>  $Remote['attr']['version'],
                'ipAddress' => $Remote['attr']['ip'],
                'isActive' => ($Remote['attr']['active']=="yes"?true:false),
                'isConnected' => ($Remote['attr']['connected']=="yes"?true:false),
                'connectionDate' => $Remote['attr']['connected_at'],
                'configurationDirectory' => (isset($Remote['attr']['configuration_directory'])?$Remote['attr']['configuration_directory']:null),
                'configurationTransferDate' => $Remote['attr']['configuration_transfered_at']
            ]);
        }
        return $remoteSchedulers;
    }
}
