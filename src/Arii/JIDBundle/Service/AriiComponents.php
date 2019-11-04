<?php
// src/Arii/JIDBundle/Service/AriiAPI.php
/*
 * Sous-couche appelée par les API et les contrôleurs
 * Recupere les infos sous forme de tableau
 * Full ORM
 */
namespace Arii\JIDBundle\Service;

class AriiComponents
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
    
    public function snakeToCamel($txt) {
        while (($p = strpos($txt,'_'))>0) {
            $txt = substr($txt,0,$p).strtoupper(substr($txt,$p+1,1)).substr($txt,$p+2);
        }
        return $txt;
    }
/*********************************************************************
 * Clusters
 *********************************************************************/
    public function Clusters($em,$Filter) {

        // On se base sur l'historique
        $Clusters = $em->getRepository("AriiJIDBundle:SchedulerClusters")->findClusters($Filter);
        foreach ($Clusters as $k=>$Cluster) {
            $date = new \DateTime();
            $Clusters[$k]['lastHeartBeat'] = $date->setTimestamp($Cluster['lastHeartBeat']);
            // On redecoupe le xml
            $xml = str_replace('"/>','',str_replace('<cluster_member ','',$Cluster['xml']));
            foreach (explode('" ',$xml) as $v) {
                list($var,$val) = explode('="',$v);
                $var = $this->snakeToCamel($var);
                $Clusters[$k][$var] = $val;
            }
           
        }
        return $Clusters;
    }

    public function ClusterMembers($em,$schedulerId) {

        // On se base sur l'historique
        $Clusters = $em->getRepository("AriiJIDBundle:SchedulerClusters")->findMembers($schedulerId);
        foreach ($Clusters as $k=>$Cluster) {
            $p = strpos($Cluster['memberId'],'/');
            if ($p>0)
                $Clusters[$k]['memberId'] = substr($Cluster['memberId'],$p+1);
            $Clusters[$k]['delay'] = time() - $Cluster['lastHeartBeat'];            
            $date = new \DateTime();
            $Clusters[$k]['lastHeartBeat'] = $date->setTimestamp($Cluster['lastHeartBeat']);
            $date2 = new \DateTime();
            $Clusters[$k]['nextHeartBeat'] = $date2->setTimestamp($Cluster['nextHeartBeat']);
            $XML = $this->tools->xml2array($Clusters[$k]['xml']);
            if (isset($XML['cluster_member_attr'])) {
                foreach ($XML['cluster_member_attr'] as $p=>$Param) {
                    switch($p) {
                        case 'running_since':
                            $Clusters[$k][$p] = new \DateTime($Param);
                            break;
                        default:
                            switch($Param) {
                                case 'yes':
                                    $Clusters[$k][$p] = 1;
                                    break;
                                default:
                                    $Clusters[$k][$p] = $Param;
                                    break;
                            }
                            break;
                    }
                }
            }
        }
        return $Clusters;
    }
    
/*********************************************************************
 * Instances
 *********************************************************************/
    public function Instances($em,$Filter) {

        // On se base sur l'historique
        $Instances = $em->getRepository("AriiJIDBundle:SchedulerInstances")->findInstances($Filter);
        foreach ($Instances as $k=>$Instance) {
            // Suppression des connexions
            $db = $Instance['dbName'];
            $db = str_replace('jdbc -class=','',$db);
            $db = substr($db,0,strpos($db,' -user='));
            $Instances[$k]['dbName'] = $db;
        }
        return $Instances;
    }

 /*********************************************************************
 * Jobs
 *********************************************************************/
    public function Jobs($em) {        
        // On se base sur l'historique
        $Jobs = $em->getRepository("AriiJIDBundle:SchedulerJobs")->findJobs();
        foreach ($Jobs as $k=>$Job) {
            if ($Job['stopped']>0)
                $Jobs[$k]['status'] = 'STOPPED';
            else
                $Jobs[$k]['status'] = 'ACTIVATED';
        }
        return $Jobs;
    }

 /*********************************************************************
 * Tasks
 *********************************************************************/
    public function Tasks($em) {        
        // On se base sur l'historique
        $Tasks = $em->getRepository("AriiJIDBundle:SchedulerTasks")->findTasks();
        foreach ($Tasks as $k=>$Task) {
            if ($Job['stopped']>0)
                $Tasks[$k]['status'] = 'STOPPED';
            else
                $Tasks[$k]['status'] = 'ACTIVATED';
        }
        return $Tasks;
    }
    
 /*********************************************************************
 * Chains
 *********************************************************************/
    public function Chains($em) {        
        // On se base sur l'historique
        $Chains = $em->getRepository("AriiJIDBundle:SchedulerJobChains")->findChains();
        foreach ($Chains as $k=>$Chain) {
            if ($Chain['stopped']>0)
                $Chains[$k]['status'] = 'STOPPED';
            else
                $Chains[$k]['status'] = 'ACTIVATED';
        }
        return $Chains;
    }

    
 /*********************************************************************
 * Orders
 *********************************************************************/
    public function HistoryOrders($em,$Filter=[]) {

        // On se base sur l'historique
        $History = $em->getRepository("AriiJIDBundle:SchedulerOrderHistory")->findOrders($Filter);
        $Orders = [];
        $Done = [];
        foreach ($History as $k=>$Order) {
            $id = $Order['spoolerId'].'#'.$Order['jobChain'].'#'.$Order['orderId'];
            if (isset($Done[$id])) continue;
            $Done[$id]=true;
            $Orders[$k] = $Order;
            if ($Order['endTime']) {
                $endTime = $Order['endTime']->getTimestamp();
                $Order['endTime']->modify($this->TZoffset." second");
            }
            else {
                $endTime = time() - $this->TZoffset;
            }
            $Orders[$k]['runtime'] = $endTime - $Order['startTime']->getTimestamp();
            $Order['startTime']->modify($this->TZoffset." second");
            // Couleur
            if ($Order['state']==='SUCCESS') {
                $Orders[$k]['status'] = 'SUCCESS';
            }
            elseif (substr($Order['state'],0,1)==='!') {
                $Orders[$k]['status'] = 'FAILURE';                
            }
            else {
                $Orders[$k]['status'] = 'RUNNING';                                
            }
        }
        return $Orders;
    }

    public function HistoryOrder($em,$orderId) {

        // On se base sur l'historique
        $Orders = $em->getRepository("AriiJIDBundle:SchedulerOrderHistory")->findOrder($orderId);
        $Order = array_pop($Orders);
        if ($Order['endTime']) {
            $endTime = $Order['endTime']->getTimestamp();
            $Order['endTime']->modify($this->TZoffset." second");
        }
        else {
            $endTime = time() - $this->TZoffset;
        }
        $Order['runtime'] = $endTime - $Order['startTime']->getTimestamp();
        $Order['startTime']->modify($this->TZoffset." second");
        return $Order;
    }

    public function Orders($em) {        
        // On se base sur l'historique
        $Orders = $em->getRepository("AriiJIDBundle:SchedulerOrders")->findOrders();
        foreach ($Orders as $k=>$Order) {
            $Orders[$k] = $this->getOrder($Order);            
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
    
 /*********************************************************************
 * OrderSteps
 *********************************************************************/
    
    public function OrderSteps($em,$history_id) {

        // On se base sur l'historique
        $Steps = $em->getRepository("AriiJIDBundle:SchedulerOrderStepHistory")->findOrderSteps($history_id);
        foreach ($Steps as $k=>$Step) {    
            if ($Step['endTime']) {
                $endTime = $Step['endTime']->getTimestamp();
                $Step['endTime']->modify($this->TZoffset." second");
            }
            else {
                $endTime = time() - $this->TZoffset;
            }
            $Steps[$k]['runtime'] = $endTime - $Step['startTime']->getTimestamp();
            $Step['startTime']->modify($this->TZoffset." second");            
            // Couleur
            if ($Step['error']) {
                $Steps[$k]['status'] = 'FAILURE';                                
                $Steps[$k]['errorText'] = substr($Step['errorText'],15);
            }
            else {
                $Steps[$k]['status'] = 'SUCCESS';
            }
        }
        return $Steps;
    }
    
/*********************************************************************
 * Spoolers
 *********************************************************************/
    public function Spoolers($em) {

        // On se base sur l'historique
        $History = $em->getRepository("AriiJIDBundle:SchedulerHistory")->findSpoolers();
        $Spoolers = [];
        $Members = [];
        foreach ($History as $H) {
            $spooler = $H['spoolerId'];
            $member = $H['clusterMemberId'];
            if (!isset($Spoolers[$spooler][$member])) {
                $Spoolers[$spooler][$member] = [
                    'Runs' => $H['Runs']
                ];
            }
        }
        return $Spoolers;
    }

/*********************************************************************
 * Runs
 *********************************************************************/
    public function Runs($em,$start,$end,$sort,$limit) {

        // On se base sur l'historique
        $Runs = $em->getRepository("AriiJIDBundle:SchedulerHistory")->findRuns($start,$end,$sort,$limit);
        foreach ($Runs as $k=>$Run) {
            $Run['log'] = base64_encode(stream_get_contents($Run['log']));
            $Runs[$k] = $Run;
               $Orders[$id]['END_TIME'] = $Order->getEndTime()->format("Y-M-d H:i:s");
               $Orders[$id]['DURATION'] =  $Order->getEndTime()->getTimestamp() - $Order->getStartTime()->getTimestamp();               
        }        
        return $Runs;
    }
    
    // Requetes sans jointure pour accélerer l'affichage
    public function OrdersDetailed($em,$start,$end,$history=0,$nested=false,$onlyWarning=false,$sort='last') {
      
       $tools = $this->tools;
       $Orders = array();
       $Chains = array();
       $Nodes = array();
       
       // On regarde les chaines stoppés
       // On complete avec les ordres stockés
       $JobChainsStopped = $em->getRepository("AriiJIDBundle:SchedulerJobChains")->findStopped();
       $nb = 0;
       $StopChain = array();
       foreach($JobChainsStopped as $JobChain) {
           $cn = $JobChain->getSpoolerId().'/'.$JobChain->getPath();
           $StopChain[$cn]=1;
       }

       // On regarde les nodes
       // On complete avec les ordres stockés
       $JobChainNodesStopped = $em->getRepository("AriiJIDBundle:SchedulerJobChainNodes")->findStopped();
       $nb = 0;
       $StopNode = $SkipNode = array();
       foreach($JobChainNodesStopped as $JobChainNode) {
           $sn = $JobChainNode->getSpoolerId().'/'.$JobChainNode->getJobChain().'/'.$JobChainNode->getOrderState();
           if ($JobChainNode->getAction() == 'next_state') $SkipNode[$sn]=1;
           if ($JobChainNode->getAction() == 'stop') $StopNode[$sn]=1;
       }
       
       $OrderHistory = $em->getRepository("AriiJIDBundle:SchedulerOrderHistory")->findStates($start,$end,$onlyWarning);
       foreach($OrderHistory as $Order) {
           if (!$nested) {
               // if (substr($Order->getOrderId(),0,1)=='.') continue;
           }
           $cn = $Order->getSpoolerId().'/'.$Order->getJobChain();
           $id = $cn.','.$Order->getOrderId();
           if (isset($Nb[$id])) {
               $Nb[$id]++;
           }
           else {
               $Nb[$id]=0;
           }
           if ($Nb[$id]>$history) continue;

           $Orders[$id]['SPOOLER_ID'] = $Order->getSpoolerId();
           $Orders[$id]['ORDER_ID'] = $Order->getOrderId();
           $Orders[$id]['HISTORY_ID'] = $Order->getHistory();
           $Orders[$id]['STATE'] = $Order->getState();
           $Orders[$id]['STATE_TEXT'] = $Order->getStateText();
           
           $Orders[$id]['DBID'] = $Order->getHistory();
           if (isset($StopChain[$cn]))
               $Orders[$id]['CHAIN_STOPPED'] = true;
           $sn = $cn.'/'.$Order->getState();
           if (isset($StopNode[$sn])) {
               $Orders[$id]['NODE_STOPPED'] = true;
           }
           if (isset($SkipNode[$sn]))
               $Orders[$id]['NODE_SKIPPED'] = true;

           // Complement
           $Orders[$id]['FOLDER'] = dirname($Order->getJobChain());
           $Orders[$id]['NAME'] = basename($Order->getJobChain());
           $Orders[$id]['NEXT_TIME'] = null;
           if ($Order->getEndTime()=='') {
               // list($start,$end,$next,$duration) = $this->date->getLocaltimes( $Order->getStartTime(), gmdate("Y-M-d H:i:s"), '', $Order->getSpoolerId(), true  );
               $Orders[$id]['END_TIME'] = '';
               $now = new \DateTime();
               $Orders[$id]['DURATION'] =  $now - $Order->getStartTime()->getTimestamp();                 
           }
           else {
               // list($start,$end,$next,$duration) = $this->date->getLocaltimes( $Order->getStartTime(), $Order->getEndTime(), '', $Order->getSpoolerId(), true  );
               $Orders[$id]['END_TIME'] = $Order->getEndTime()->format("Y-M-d H:i:s");
               $Orders[$id]['DURATION'] =  $Order->getEndTime()->getTimestamp() - $Order->getStartTime()->getTimestamp();               
           }
           $Orders[$id]['START_TIME'] = $Order->getStartTime()->format("Y-M-d H:i:s");           
       }

       $DBOrders = $em->getRepository("AriiJIDBundle:SchedulerOrders")->findStates();
       //when we want to store the planned orders, we also need to store the job chains which the planned orders belong.
       $nb = 0;
       foreach($DBOrders as $Order) {
           $cn = $Order->getSpoolerId().'/'.$Order->getJobChain();
           $on = $cn.','.$Order->getId();
           if (!isset($Orders[$on])) continue;

           if ($Order->getOrderXml()!=null)
           {
               if (gettype($Order->getOrderXml())=='object') {
                   $order_xml = $tools->xml2array($Order->getOrderXml()->load());
               }
               else {
                   $order_xml = $tools->xml2array($Order->getOrderXml());
               }
               $setback = 0; $setback_time = '';
               if (isset($order_xml['order_attr']['suspended']) && $order_xml['order_attr']['suspended'] == "yes")
               {
                   $Orders[$on]['SUSPENDED'] = true;
               }
               elseif (isset($order_xml['order_attr']['setback_count']))
               {
                   $Orders[$on]['SETBACK'] = true;
                   $Orders[$on]['SETBACK_COUNT'] = $order_xml['order_attr']['setback_count'];
                   if (isset($order_xml['order_attr']['setback']))
                       $Orders[$on]['SETBACK_TIME'] = $order_xml['order_attr']['setback'];
                   else
                       $Orders[$on]['SETBACK_TIME'] = '';
               }

               if (isset($order_xml['order_attr']['at'])) {                   
                   $at = new \DateTime ( $order_xml['order_attr']['at'] );
                   $Orders[$on]['NEXT_TIME'] = new \DateTime ( $order_xml['order_attr']['at'] );
               }
               elseif (isset($order_xml['order_attr']['start_time'])) {
                   $Orders[$on]['NEXT_TIME'] = new \DateTime ( $order_xml['order_attr']['at'] );
               }
               else {
                   $at = '';
               }
               $hid = 0;
               if (isset($order_xml['order_attr']['history_id'])) {
                   $hid = $order_xml['order_attr']['history_id'];
               }
           }

       }
       $New = array();
       $Keys = array_keys($Orders);
       // sort($Keys);
        $ColorStatus = $this->portal->getColors();
        foreach ( $Keys as $on) {
           $line = $Orders[$on];
                   // Calcul du statut
           if (isset($line['SUSPENDED'])) {
               $status = 'SUSPENDED';
           }
           elseif (isset($line['CHAIN_STOPPED'])) {
               $status = 'CHAIN STOP.';
           }
           elseif (isset($line['NODE_STOPPED'])) {
               $status = 'NODE STOP.';
           }
           elseif (isset($line['NODE_SKIPPED'])) {
               $status = 'NODE SKIP.';
           }
           elseif (isset($line['SETBACK'])) {
               $status = 'SETBACK';
           }
           elseif ($line['END_TIME']=='') {
               $status = 'RUNNING';
           }
           elseif (substr($line['STATE'],0,1)=='!') {
               $status = 'FAILURE';
           }
           else {
               $status = 'SUCCESS';
           }
           if (($onlyWarning)and ($status == 'SUCCESS')) continue;
           $Orders[$on]['STATUS'] = $status;
           if  ($line['NEXT_TIME']==$line['START_TIME'])
               $Orders[$on]['NEXT_TIME']='';

            $Orders[$on]['COLOR'] = (isset($ColorStatus[$status]['bgcolor'])?$ColorStatus[$status]['bgcolor']:'black');
                
           $New[$on] = $Orders[$on];
       }
       return $New;
    }

   //ajout de la variable bool pour dissocier les jobs avec ou sans chaines
   public function HistoryJobs($history_max=0,$ordered = 0,$onlyWarning= 1,$next=1, $name="", $spooler="") {

     $data = $this->db->Connector('data');

     $sql = $this->sql;
     $date = $this->date;

     $Fields = array (
     '{spooler}'    => 'SPOOLER_ID',
     '{job_name}'   => 'PATH' );

     $qry = $sql->Select(array('SPOOLER_ID','PATH','STOPPED','NEXT_START_TIME'))
     .$sql->From(array('SCHEDULER_JOBS'))
     .$sql->Where($Fields)
     .$sql->OrderBy(array('SPOOLER_ID','PATH'));

     $res = $data->sql->query( $qry );
     while ($line = $data->sql->get_next($res)) {
       $jn = $line['SPOOLER_ID'].'/'.$line['PATH'];
       if ($line['STOPPED']=='1' ) {
         $Stopped[$jn] = true;
       }
       if ($line['NEXT_START_TIME']!='' ) {
         $Next_start[$jn] = $line['NEXT_START_TIME'];
       }
     }

     /* On prend l'historique */
     $Fields = array (
         '{spooler}'    => 'SPOOLER_ID',
         '{job_name}'   => 'JOB_NAME',
         '{error}'      => 'ERROR',
         '{next_start_time}' => 'START_TIME',
         '{!(spooler)}' => 'JOB_NAME' );

    if( $name != "")
      $Fields['JOB_NAME'] = $name;

    if (!$ordered)
      $Fields['{standalone}'] = 'CAUSE';

    $qry = $sql->Select(array('ID','SPOOLER_ID','JOB_NAME','START_TIME','END_TIME','ERROR','ERROR_TEXT','EXIT_CODE','CAUSE','PID'))
          .$sql->From(array('SCHEDULER_HISTORY'))
          .$sql->Where($Fields);

    if($spooler != "")
      $qry .= ' and SPOOLER_ID = \''.$spooler.'\' ';

    $qry .= $sql->OrderBy(array('SPOOLER_ID','JOB_NAME','START_TIME desc'));
    $res = $data->sql->query( $qry );

    //Traitement
    $Jobs = array();
    while ($line = $data->sql->get_next($res)) {

        $id = $line['ID'];

        if (isset($Stopped[$id]) and ($Stopped[$id]==1)) {
          if ($line['END_TIME']=='')
          $status = 'STOPPING';
          else
          $status = 'STOPPED';
        }
        elseif ($line['END_TIME']=='') {
          $status = 'RUNNING';
        } // cas des historique
        elseif ($line['ERROR']>0) {
          $status = 'FAILURE';
        }
        else {
          $status = 'SUCCESS';
        }

        if (($onlyWarning) and ($status == 'SUCCESS')) continue;

        $Jobs[$id]['id'] = $line['ID'];
        $Jobs[$id]['spooler'] = $line['SPOOLER_ID'];
        $Jobs[$id]['folder']  = dirname($line['JOB_NAME']);
        $Jobs[$id]['name']    = basename($line['JOB_NAME']);
        $Jobs[$id]['dbid'] = $line['ID'];
        $Jobs[$id]['status'] = $status;
        $Jobs[$id]['exit'] = $line['EXIT_CODE'];
        $Jobs[$id]['pid'] = $line['PID'];
        $Jobs[$id]['cause'] = $line['CAUSE'];
        $Jobs[$id]['error'] = $line['ERROR'];
        $Jobs[$id]['error_text'] = $line['ERROR_TEXT'];
        
        if (isset($Stopped[$id]))
        $Jobs[$id]['stopped'] = true;
        if (isset($Next_start[$id]))
        $Jobs[$id]['next_start'] = $Next_start[$id];

        if ($status=='RUNNING') {
          list($start,$end,$next,$duration) = $date->getLocaltimes( $line['START_TIME'],gmdate("Y-M-d H:i:s"),'', $line['SPOOLER_ID'], false  );
          $Jobs[$id]['end'] = '';
        }
        else {
          list($start,$end,$next,$duration) = $date->getLocaltimes( $line['START_TIME'],$line['END_TIME'],'', $line['SPOOLER_ID'], false  );
          $Jobs[$id]['end'] = $end;
        }
        $Jobs[$id]['start'] = $start;
        $Jobs[$id]['next'] = $next;
        $Jobs[$id]['duration'] = $duration;
    }
    return $Jobs;
  }
    public function Parameters($id) {

        $data = $this->db->Connector('data');
        $sql = $this->sql;        
        $qry = $sql->Select(array('PARAMETERS'))
                .$sql->From(array('SCHEDULER_HISTORY'))
                .$sql->Where(array('ID' => $id));

        $res = $data->sql->query($qry);
        $line = $data->sql->get_next($res);

        if (is_object($line['PARAMETERS']))
            return $line['PARAMETERS']->load();
        else 
            return $line['PARAMETERS'];
    }
    
}

