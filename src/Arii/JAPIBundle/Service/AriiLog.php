<?php
namespace Arii\JAPIBundle\Service;

class AriiLog
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

        $f= fopen("http://$host:$port".$command,"r");
        if (!$f) {
            print "$host:$port !";
            exit();
        }
        $data = '';
        while(!feof($f)) {
            $data .= fread($f,10240);
        }
        fclose($f);
        return $data;
    }

    // Ne marche pas ?
    function Order($instance,$orderName,$logId,$Filter) {
        $Data = $this->get($instance,'/show_log?order='.$orderName.'&history_id='.$logId);
        return $Data; 
    }

}
