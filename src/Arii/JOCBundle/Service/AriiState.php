<?php
// src/Arii/JOCBundle/Service/AriiState.php
/*
 * Recupere les données et fournit un tableau pour les composants DHTMLx
 */ 
namespace Arii\JOCBundle\Service;

class AriiState
{
    protected $em;
    protected $db;
    protected $sql;
    protected $date;
    
    public function __construct (  
            \Doctrine\ORM\EntityManager $entityManager,
            \Arii\CoreBundle\Service\AriiDB $db, 
            \Arii\CoreBundle\Service\AriiSQL $sql,
            \Arii\CoreBundle\Service\AriiDate $date ) {
        $this->em = $entityManager;
        $this->db = $db;
        $this->sql = $sql;
        $this->date = $date;
    }

 /*********************************************************************
 * Orders
 *********************************************************************/
    public function Orders() {

        // On se base sur l'historique
        $History = $this->em->getRepository("AriiJOCBundle:Orders")->findOrders();
        $Orders = [];
        foreach ($History as $k=>$Order) {    
            $Orders[$k] = $Order;
            // $endTime = time() - $this->TZoffset;
            $endTime = time();
            if ($Order['start_time']) {
                $Orders[$k]['runtime'] = $endTime - $Order['start_time']->getTimestamp();            
            }
            else {
                $Orders[$k]['runtime'] = '';            
            }
            // $Order['start_time']->modify($this->TZoffset." second");
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

    public function Order($em,$orderId) {

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
    
    
    
    
    
    
    

   public function Jobs($ordered = false, $onlyWarning= true) {   
        $date = $this->date;        
        $sql = $this->sql;
        $db = $this->db;
        $data = $db->Connector('data');

        // Jobs
        $Fields = array( '{spooler}' => 'NAME',
                         '{job_name}'   => 'PATH' );

        if (!$ordered) {
            $Fields['{standalone2}'] = 'ORDERED';
        }
        if ($onlyWarning) {
            $Fields['{!pending}'] = 'j.STATE';
        }
        $qry = $sql->Select(array('s.NAME as SPOOLER',
                        'j.ID','j.PATH','j.STATE','j.STATE_TEXT','j.TITLE','j.UPDATED','j.ORDERED','j.NEXT_START_TIME','j.LAST_INFO','j.LOG_LEVEL','j.HIGHEST_LEVEL','j.LAST_WARNING','j.LAST_ERROR','j.ERROR','j.WAITING_FOR_PROCESS','j.ORDERED','j.PROCESS_CLASS','j.SCHEDULE' ))
                .$sql->From(array('JOC_JOBS j'))
                .$sql->LeftJoin('JOC_SPOOLERS s',array('j.SPOOLER_ID','s.ID'))
                .$sql->Where($Fields)
                .$sql->OrderBy(array('s.NAME','j.PATH'));

        $res = $data->sql->query($qry);
        $Jobs = array();
        while ($line = $data->sql->get_next($res))
        {            
            $jn = $line['SPOOLER'].'/'.$line['PATH'];
            $jn = str_replace("//", "/", $jn);
            $joid = $line['ID'];

            $Jobs[$jn] =$line;

            $Jobs[$jn]['DBID'] = $joid;
            $Jobs[$jn]['FOLDER'] = dirname($line['PATH']);
            $Jobs[$jn]['NAME'] = basename($line['PATH']);
            # Remontees d'informations
            $Jobs[$jn]['STATE'] = strtoupper($line['STATE']);
        }
        return $Jobs;
   }

   public function Tasks($job) {   
        $date = $this->date;        
        $sql = $this->sql;
        $db = $this->db;
        $data = $db->Connector('data');
        
        $qry = $sql->Select(array('ID','TASK','STATE','RUNNING_SINCE','START_AT','ENQUEUED','CAUSE','STEPS','PID','PRIORITY','FORCE_START','HIGHEST_LEVEL','LAST_INFO','LAST_ERROR','LAST_WARNING' ))
                .$sql->From(array('JOC_TASKS'))
                .$sql->Where( array(  'JOB_ID' => $job ) )
                .$sql->OrderBy(array('ID desc'));

        $res = $data->sql->query($qry);
        $Tasks = array();
        while ($line = $data->sql->get_next($res))
        {   
            $jn = $line['ID'];            
            $Tasks[$jn] = $line;
        }
        return $Tasks;
   }
   
   public function Spoolers() {   
        $date = $this->date;        
        $sql = $this->sql;
        $db = $this->db;
        $data = $db->Connector('data');
        
        // Jobs
        $Fields = array( '{spooler}' => 's.NAME' );
        $qry = $sql->Select(array('s.ID','s.NAME as SPOOLER','s.STATE','s.VERSION','s.HOST','s.IP_ADDRESS','s.TCP_PORT','s.NEED_DB','s.UPDATED','s.TIME'))
               .$sql->From(array('JOC_SPOOLERS s'))
               .$sql->OrderBy(array('s.NAME'));

        $res = $data->sql->query($qry);
        $Spoolers = array();
        while ($line = $data->sql->get_next($res))
        {            
           // $sn = $line['IP_ADDRESS'].':'.$line['TCP_PORT'];
            $sn = $line['ID']; // pour les doublons
            $Spoolers[$sn] = $line;
            $Spoolers[$sn]['DBID'] = $line['ID'];
            
            // Calcul du statut
            date_default_timezone_set("UTC"); 
            $last = time() - strtotime( $line['TIME'] );
            $Spoolers[$sn]['LAST_UPDATE'] = $last;
            if ($last>300) {
                $status = 'LOST';
            }
            elseif ($line['STATE']=='paused') {
                $status = 'PAUSED';
            }
            elseif ($line['STATE']=='running') {
                $status = 'RUNNING';
            }
            elseif ($line['SPOOLER']=='') {
                $status = 'UNKNOWN';
            }
            else {
                $status = $line['STATE'];
            }
            $Spoolers[$sn]['STATUS'] = $status;
            $Spoolers[$sn]['TIME'] = $date->Date2Local($line['TIME'],$line['SPOOLER']);
        }
        return $Spoolers;
   }

   public function RemoteSchedulers() {   
        $date = $this->date;        
        $sql = $this->sql;
        $db = $this->db;
        $data = $db->Connector('data');
        
        // Jobs
        $Fields = array( '{spooler}' => 's.NAME' );
        $qry = $sql->Select(array('s.NAME as SUPERVISOR','rs.ID','rs.NAME','rs.HOSTNAME','rs.IP','rs.PORT','rs.VERSION','rs.UPDATED'))
               .$sql->From(array('JOC_REMOTE_SCHEDULERS rs'))
               .$sql->leftJoin('JOC_SPOOLERS s',array('rs.SPOOLER_ID','s.ID'))
               .$sql->OrderBy(array('s.NAME'));

        $res = $data->sql->query($qry);
        $RemoteSchedulers = array();
        while ($line = $data->sql->get_next($res))
        {            
           // $sn = $line['IP_ADDRESS'].':'.$line['TCP_PORT'];
            $sn = $line['ID']; // pour les doublons
            $RemoteSchedulers[$sn] = $line;
            
            date_default_timezone_set("UTC"); 
            $last = time() - strtotime( $line['UPDATED'] );
            if ($last > 60 ) {
                $RemoteSchedulers[$sn]['STATUS'] = 'DISCONNECTED';
            }
            else {
                $RemoteSchedulers[$sn]['STATUS'] = 'CONNECTED';
            }
        }
        return $RemoteSchedulers;
   }
   
   public function OrdersOLD( $nested=0, $onlyWarning= true, $sort='last', $id=-1) {
        $date = $this->date;
        $sql = $this->sql;
        $db = $this->db;
        $data = $db->Connector('data');

        $Fields = array( '{spooler}' => 'fs.NAME',
                         '{job_chain}'   => 'fo.PATH',
                         '{order_id}' => 'fo.NAME' );
        if ($id>=0)
            $Fields['fo.ID'] = $id;
        
        switch ($sort) {
            case 'spooler':
                $Sort = array('fs.NAME','fo.PATH');
                break;
            case 'chain':
                $Sort = array('fo.PATH','fs.NAME');
                break;
            default:
                $Sort = array('fo.NEXT_START_TIME desc','fo.START_TIME desc','fs.NAME','fo.PATH');
                break;
        }
    
        $qry = $sql->Select(array( 'fs.NAME as SPOOLER_ID','fs.HOST','fo.PATH','fo.ID','fo.NAME','fo.START_TIME','fo.NEXT_START_TIME','fo.TITLE','fo.STATE','fo.STATE_TEXT','fo.SUSPENDED','fo.IN_PROCESS_SINCE','fo.TASK_ID','fo.HISTORY_ID','fo.SETBACK','fo.SETBACK_COUNT' ))
                .$sql->From(array('JOC_ORDERS fo'))
                .$sql->LeftJoin('JOC_JOB_CHAIN_NODES fn',array('fo.JOB_CHAIN_NODE_ID','fn.ID'))
                .$sql->LeftJoin('JOC_JOB_CHAINS fc',array('fn.JOB_CHAIN_ID','fc.ID'))
                .$sql->LeftJoin('JOC_SPOOLERS fs',array('fc.SPOOLER_ID','fs.ID'))
                .$sql->Where($Fields)
                .$sql->OrderBy($Sort);        

        $Orders = array();
        $res = $data->sql->query($qry);
        while ($line = $data->sql->get_next($res))
        {
            $id = $line['ID'];
            
            if ($line['SUSPENDED']==1) {
                $status = 'SUSPENDED';                
            }
            elseif ($line['SETBACK']!='') {
                $status = 'SETBACK';                
            }
            elseif ($line['START_TIME']!='') {
                $status = 'RUNNING';                
            }
            elseif (substr($line['NEXT_START_TIME'],0,4)=='2038') {
                $status = 'DONE';
            }
            elseif ($line['NEXT_START_TIME']!='') {
                $status = 'WAITING';                
            }
            else {
                $status = 'UNKNOWN!';
            }
            if (($onlyWarning) and (($status =='DONE') or ($status =='WAITING'))) continue;
            $Orders[$id] = $line;
            $Orders[$id]['STATUS'] = $status;
            $p = strpos($line['PATH'],',');
            $Orders[$id]['JOB_CHAIN'] = substr($line['PATH'],0,$p);
            $Orders[$id]['ORDER'] = substr($line['PATH'],$p+1); 
            if ($line['START_TIME']!='')
                $Orders[$id]['START_TIME'] = $date->Date2Local($line['START_TIME'],$line['SPOOLER_ID']);
            else 
                $Orders[$id]['START_TIME'] = '';
            if (($line['NEXT_START_TIME']=='') or ($status=='DONE')) {
                $Orders[$id]['NEXT_START_TIME'] = '';
            }
            else {
                $Orders[$id]['NEXT_START_TIME'] = $date->Date2Local($line['NEXT_START_TIME'],$line['SPOOLER_ID']);
            }
            if ($line['SPOOLER_ID']!=$line['HOST'])
                $Orders[$id]['SPOOLER_ID'] .= '/'.$line['HOST'];
        }
        return $Orders;
   }

   public function Locks() {   
        $date = $this->date;        
        $sql = $this->sql;
        $db = $this->db;
        $data = $db->Connector('data');
        
        // Jobs
        $Fields = array( '{spooler}' => 's.NAME' );
        $qry = $sql->Select(array('s.NAME as SPOOLER','s.HOST','l.ID','l.NAME','l.PATH','l.MAX_NON_EXCLUSIVE','l.IS_FREE','l.STATE'))
               .$sql->From(array('JOC_LOCKS l'))
               .$sql->LeftJoin('JOC_SPOOLERS s',array('l.SPOOLER_ID','s.ID'))
               .$sql->Where($Fields)
               .$sql->OrderBy(array('s.NAME','l.PATH'));

        $res = $data->sql->query($qry);
        $Locks = array();
        while ($line = $data->sql->get_next($res))
        {         
            $id = $line['ID'];
            $Locks[$id]=$line;
            $Locks[$id]['FOLDER'] = dirname($line['PATH']);
            if (strtolower($line['SPOOLER'])!=strtolower($line['HOST']))
                $Locks[$id]['SPOOLER'] .= '/'.$line['HOST'];
        }
        return $Locks;
   }

   public function LocksUse() {   
        $date = $this->date;        
        $sql = $this->sql;
        $db = $this->db;
        $data = $db->Connector('data');
        
        // Jobs
        $Fields = array( '{spooler}' => 's.NAME' );
        $qry = $sql->Select(array(  'j.STATE',
                                    's.NAME as SPOOLER',
                                    'l.PATH','max(l.EXCLUSIVE_USE) as EXCLUSIVE','max(l.IS_MISSING) as IS_MISSING','count(j.ID) as JOBS'
                                    ))
               .$sql->From(array('JOC_LOCKS_USE l'))
               .$sql->LeftJoin('JOC_JOBS j',array('l.JOB_ID','j.ID'))
               .$sql->LeftJoin('JOC_SPOOLERS s',array('l.SPOOLER_ID','s.ID'))
               .$sql->Where($Fields)
               .$sql->GroupBy(array('s.NAME','j.STATE','l.PATH'))                 
               .$sql->OrderBy(array('s.NAME','l.PATH'));

        $res = $data->sql->query($qry);
        $Locks = array();
        while ($line = $data->sql->get_next($res))
        {         
            $id = $line['PATH'];
            $Locks[$id]['NAME'] = basename($line['PATH']);
            $Locks[$id]['FOLDER'] = dirname($line['PATH']);
            $state = strtoupper($line['STATE']);
            $Locks[$id][$state] = $line['JOBS'];
            foreach (array('SPOOLER','EXCLUSIVE','IS_MISSING') as $k) {
                $Locks[$id][$k] = $line[$k];
            }
        }
        return $Locks;
   }

}
