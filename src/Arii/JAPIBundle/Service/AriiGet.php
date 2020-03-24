<?php
namespace Arii\JAPIBundle\Service;

class AriiGet
{
    protected $tools;
    protected $convert;
    
    public function __construct(
            \Arii\CoreBundle\Service\AriiTools $tools,
            AriiConvert $convert ) {
        $this->tools = $tools;
        $this->convert = $convert;
    }

    public function get($instance='localhost:4444',$command='') {
       set_time_limit ( 900 );
        // pour l'instant, que du host:port
        if (($p=strpos($instance, ':'))>0)
            list($host,$port) = split(':',$instance);
        else 
            return;

        $f= @fopen("http://$host:$port/".str_replace(' ','%20',$command),"r");
        if (!$f) {
            print "$host:$port !";
            exit();
        }
        $data = '';
        while(!feof($f)) {
            $data .= fread($f,10240);
        }
        fclose($f);
        return $this->tools->xml2array( $data , 1, 'attributes');
    }

    // Ne marche pas ?
    function Chains($instance,$Filter) {
        $Data = $this->get($instance,'<show_job_chains max_order_history="99" what="job_chains"/>');
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
    
    function Chain($instance,$name,$Filter) {
        $Data = $this->get($instance,'<show_job_chain job_chain="'.$Filter['path'].'/'.$name.'" max_order_history="999" max_orders="999"/>');
        if (!isset($Data['spooler']['answer']['job_chain']))
            return $Data;

        $Chain = $this->convert->Chain($Data['spooler']['answer']['job_chain']);
        return $Chain;
    }

    function Order($instance,$chain,$name,$Filter) {
        $Data = $this->get($instance,'<show_order job_chain="'.$Filter['path'].'/'.$chain.'" order="'.$name.'"/>');
        if (!isset($Data['spooler']['answer']['order']))
            return $Data;

        $Order = $this->convert->Order($Data['spooler']['answer']['order']);
        return $Order;
    }
    
}
