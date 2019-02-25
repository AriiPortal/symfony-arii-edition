<?php
// src/Arii/JIDBundle/Service/AriiHistory.php
/*
 * Recupere les données et fournit un tableau pour les composants DHTMLx
 */
namespace Arii\JIDBundle\Service;

class AriiHistory
{
    protected $db;
    protected $sql;
    protected $date;
    protected $tools;

    protected $portal;

    public function __construct (
            \Arii\CoreBundle\Service\AriiPortal $portal,
            \Arii\CoreBundle\Service\AriiDHTMLX $db,
            \Arii\CoreBundle\Service\AriiSQL $sql,
            \Arii\CoreBundle\Service\AriiDate $date,
            \Arii\CoreBundle\Service\AriiTools $tools ) {

        $this->portal = $portal;
        $this->date = $date;
        $this->tools = $tools;
        
        $this->db = $db;
        // on récupère le driver de la base en cours
        $driver = $db->getDriver();
        // on l'injecte dans le module sql
        $this->sql = $sql;
        $this->sql->setDriver($driver);        
    }

    // on force la DB
    public function setDB($name) {
        $this->db->setDB($name);
        $driver = $this->db->getDriver();
        $this->sql->setDriver($driver);        
    }
/*********************************************************************
 * Informations de connexions
 *********************************************************************/

   //ajout de la variable bool pour dissocier les jobs avec ou sans chaines
   public function Jobs($history_max=0,$ordered = 0,$only_warning= 1,$next=1, $name="", $spooler="") {

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

        if (($only_warning) and ($status == 'SUCCESS')) continue;

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

  public function Orders($history=0,$nested=false,$only_warning=true,$sort='last') {

       $data = $this->db->Connector('data');
       $sql = $this->sql;
       $date = $this->date;
       $tools = $this->tools;
       $Orders = array();
       $Chains = array();
       $Nodes = array();

       // On regarde les chaines stoppés
       // On complete avec les ordres stockés
       $Fields = array (
           '{spooler}'    => 'SPOOLER_ID',
           'STOPPED'  => 1 );

       $qry = $sql->Select( array('SPOOLER_ID','PATH') )
               .$sql->From( array('SCHEDULER_JOB_CHAINS') )
               .$sql->Where( $Fields );

       $res = $data->sql->query( $qry );
       $nb = 0;
       $StopChain = array();
       while ($line = $data->sql->get_next($res)) {
           $cn = $line['SPOOLER_ID'].'/'.$line['PATH'];
           $StopChain[$cn]=1;
       }
       // On regarde les nodes
       // On complete avec les ordres stockés
       $Fields = array (
           '{spooler}'    => 'SPOOLER_ID',
           '{job_chain}'  => 'JOB_CHAIN',
           'ACTION'  => '(!null)' );
       $qry = $sql->Select( array('SPOOLER_ID','JOB_CHAIN','ORDER_STATE','ACTION') )
               .$sql->From( array('SCHEDULER_JOB_CHAIN_NODES') )
               .$sql->Where( $Fields );
       
       $res = $data->sql->query( $qry );
       $nb = 0;
       $StopNode = $SkipNode = array();
       while ($line = $data->sql->get_next($res)) {
           $sn = $line['SPOOLER_ID'].'/'.$line['JOB_CHAIN'].'/'.$line['ORDER_STATE'];
           if ($line['ACTION'] == 'next_state') $SkipNode[$sn]=1;
           if ($line['ACTION'] == 'stop') $StopNode[$sn]=1;
       }
       // Historique des ordres
       $Fields = array (
           '{spooler}'    => 'soh.SPOOLER_ID',
           '{job_chain}'   => 'soh.JOB_CHAIN',
           '{next_start_time}' => 'soh.START_TIME' );

       switch ($sort) {
           case 'spooler':
               $Sort = array('soh.SPOOLER_ID','soh.JOB_CHAIN','soh.HISTORY_ID desc','sosh.STEP desc');
               break;
           case 'chain':
               $Sort = array('soh.JOB_CHAIN','soh.SPOOLER_ID','soh.HISTORY_ID desc','sosh.STEP desc');
               break;
           default:
               $Sort = array('soh.HISTORY_ID desc','sosh.STEP desc');
               break;
       }

       $qry = $sql->Select(array('soh.HISTORY_ID','soh.TITLE','soh.START_TIME','soh.END_TIME','soh.SPOOLER_ID','soh.ORDER_ID','soh.JOB_CHAIN','soh.STATE','soh.STATE_TEXT','sosh.ERROR'))
               .$sql->From(array('SCHEDULER_ORDER_HISTORY soh'))
               .$sql->LeftJoin('SCHEDULER_ORDER_STEP_HISTORY sosh',array('soh.HISTORY_ID','sosh.HISTORY_ID'))
               .$sql->Where($Fields)
               .$sql->OrderBy($Sort);

       $res = $data->sql->query( $qry );
       while ($line = $data->sql->get_next($res)) {
           if (!$nested) {
               if (substr($line['ORDER_ID'],0,1)=='.') continue;
           }
           $cn = $line['SPOOLER_ID'].'/'.$line['JOB_CHAIN'];
           $id = $cn.','.$line['ORDER_ID'];
           if (isset($Nb[$id])) {
               $Nb[$id]++;
           }
           else {
               $Nb[$id]=0;
           }
           if ($Nb[$id]>$history) continue;

           $Orders[$id]['DBID'] = $line['HISTORY_ID'];
           $Orders[$id] = $line;
           if (isset($StopChain[$cn]))
               $Orders[$id]['CHAIN_STOPPED'] = true;
           $sn = $cn.'/'.$line['STATE'];
           if (isset($StopNode[$sn])) {
               $Orders[$id]['NODE_STOPPED'] = true;
           }
           if (isset($SkipNode[$sn]))
               $Orders[$id]['NODE_SKIPPED'] = true;

           // Complement
           $Orders[$id]['FOLDER'] = dirname($line['JOB_CHAIN']);
           $Orders[$id]['NAME'] = basename($line['JOB_CHAIN']);
           $Orders[$id]['NEXT_TIME'] = '';
           if ($line['END_TIME']=='') {
               list($start,$end,$next,$duration) = $date->getLocaltimes( $line['START_TIME'], gmdate("Y-M-d H:i:s"), '', $line['SPOOLER_ID'], true  );
               $Orders[$id]['END_TIME'] = '';
           }
           else {
               list($start,$end,$next,$duration) = $date->getLocaltimes( $line['START_TIME'], $line['END_TIME'], '', $line['SPOOLER_ID'], true  );
               $Orders[$id]['END_TIME'] = $end;
           }
           $Orders[$id]['START_TIME'] = $start;
           $Orders[$id]['DURATION'] = $duration;

       }
       // On complete avec les ordres stockés
       $Fields = array (
           '{spooler}'    => 'SPOOLER_ID',
           '{job_chain}'  => 'JOB_CHAIN',
           '{order_id}'  => 'ID',
/*          'created_time' => 'CREATED_TIME',
*/          '{start_time}' => 'MOD_TIME'
               );

       $qry = $sql->Select( array('SPOOLER_ID','JOB_CHAIN','ID as ORDER_ID','PRIORITY','STATE as ORDER_STATE','STATE_TEXT','TITLE','CREATED_TIME','MOD_TIME','ORDERING','INITIAL_STATE','ORDER_XML' ) )
               .$sql->From( array('SCHEDULER_ORDERS') )
               .$sql->Where( $Fields )
               .$sql->OrderBy( array('ORDERING desc') );
       
       //when we want to store the planned orders, we also need to store the job chains which the planned orders belong.
       $res = $data->sql->query( $qry );
       $nb = 0;
       while ($line = $data->sql->get_next($res)) {
           $cn = $line['SPOOLER_ID'].'/'.$line['JOB_CHAIN'];
           $on = $cn.','.$line['ORDER_ID'];
           if (!isset($Orders[$on])) continue;

           if ($line['ORDER_XML']!=null)
           {
               if (gettype($line['ORDER_XML'])=='object') {
                   $order_xml = $tools->xml2array($line['ORDER_XML']->load());
               }
               else {
                   $order_xml = $tools->xml2array($line['ORDER_XML']);
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
                   $at = $date->Date2Local($order_xml['order_attr']['at'],$line['SPOOLER_ID']);
                   $Orders[$on]['NEXT_TIME'] = $date->Date2Local($order_xml['order_attr']['at'],$line['SPOOLER_ID'],true);
               }
               elseif (isset($order_xml['order_attr']['start_time'])) {
                   $Orders[$on]['NEXT_TIME'] = $date->Date2Local($order_xml['order_attr']['start_time'],$line['SPOOLER_ID'],true);
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
           elseif (substr($line['END_TIME'],0,1)=='!') {
               $status = 'FATAL';
           }
           elseif ($line['ERROR']==0) {
               $status = 'SUCCESS';
           }
           else {
               $status = 'FAILURE';
           }
           if (($only_warning)and ($status == 'SUCCESS')) continue;
           $Orders[$on]['STATUS'] = $status;
           if  ($line['NEXT_TIME']==$line['START_TIME'])
               $Orders[$on]['NEXT_TIME']='';

           $New[$on] = $Orders[$on];
       }
       return $New;
  }
  
    public function Spoolers() {

        $data = $this->db->Connector('data');
        $sql = $this->sql;
        $Spoolers = array();
          
        $Fields = array (
            '{spooler}'    => 'SPOOLER_ID',
            '{start_time}' => 'START_TIME',
            '{end_time}'   => 'END_TIME' );

        $qry = $sql->Select(array('SPOOLER_ID','ERROR','END_TIME','count(ID) as NB'))
        .$sql->From(array('SCHEDULER_HISTORY'))
        .$sql->Where($Fields)
        .$sql->GroupBy(array('SPOOLER_ID','ERROR','END_TIME'))
        .$sql->OrderBy(array('SPOOLER_ID'));

        $res = $data->sql->query( $qry );
        while ($line = $data->sql->get_next($res)) {
            $spooler = $line['SPOOLER_ID'];
            $Spoolers[$spooler] = $line;        
        }
        
        return $Spoolers;
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

