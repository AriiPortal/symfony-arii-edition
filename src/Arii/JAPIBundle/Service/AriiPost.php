<?php
namespace Arii\JAPIBundle\Service;

class AriiPost
{
    protected $tools;
    
    public function __construct( \Arii\CoreBundle\Service\AriiTools $tools ) {
        $this->tools = $tools;
    }

    public function command($instance='localhost:4444',$cmd='') {
        set_time_limit ( 900 );

        // pour l'instant, que du host:port
        if (($p=strpos($instance, ':'))>0)
            list($host,$port) = split(':',$instance);
        else 
            return;
        $ch = curl_init();
        $url = "http://".$host.":".$port;   
        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$cmd);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        $content = curl_exec($ch);
        if ($content === FALSE) {
            printf("cUrl error (#%d): %s<br/>$spooler<br/>$url\n", curl_errno($ch),
            htmlspecialchars(curl_error($ch)));
            $this->portal->AuditLog($host, $cmd, "ERROR","JID", sprintf("cUrl error (#%d): %s<br>\n", curl_errno($ch),
            htmlspecialchars(curl_error($ch))));
            $this->portal->ErrorLog("!ERROR: Can not Open URL: ".$url, 0, __FILE__, __LINE__, "ERROR at: ".__FILE__." function: ".__FUNCTION__." line: ".__LINE__.": !ERROR: Connection failed! Please make sure the JobScheduler have started!");
            return array('ERROR'=>'CONNECT ON '.$url);
        }
        curl_close($ch);
        return $this->tools->xml2array( $content , 1, 'attributes');
    }

    function Answer($Data) {
        if (isset($Data['spooler']['answer']['ok'])) {
            return [
                'answer' => 'ok',
                'time' => $Data['spooler']['answer']['attr']['time']
            ];
        }
        return $Data;
    }
    
    // Instance 
    function Instance($instance,$Parameters) {
        $Data = $this->command($instance,'<modify_spooler cmd="'.$Parameters['state'].'" timeout="'.$Parameters['timeout'].'"/>');
        return $this->Answer($Data);
    }

    // Chain
    function putChain($instance,$chain,$Parameters) {
        $command = '<job_chain.modify job_chain="'.$Parameters['path'].'/'.$chain.'" state="'.$Parameters['state'].'"/>';

        $Data = $this->command($instance,$command);
        return $this->Answer($Data);
    }
    
    // Order
    function putOrder($instance,$orderId,$Parameters) {
        list($chain,$order)=split(',',$orderId);
        $command = '<modify_order order="'.$order.'" job_chain="'.$Parameters['path'].'/'.$chain.'"';
        switch ($Parameters['action']) {
            case 'reset':
                $command .= ' action="reset"';
                break;
            case 'suspend':
                $command .= ' suspended="true"';
                break;
            case 'setback':
                $command .= ' setback="true"';
                break;
        }
        if (isset($Parameters['title']))
            $command .= ' title="'.$Parameters['title'].'"';
        
        $command .= '/>';
        $Data = $this->command($instance,$command);
        return $this->Answer($Data);
    }

    function createOrder($instance,$Parameters) {
        if (isset($Parameters['isPersistent']) and ($Parameters['isPersistent']))
            $type = 'order';
        else
            $type = 'add_order';
        
        $command = '<'.$type.' id="'.$Parameters['orderName'].'" job_chain="'.$Parameters['path'].'/'.$Parameters['chainName'].'"';
        if (isset($Parameters['title']))
            $command .= ' title="'.$Parameters['title'].'"';
        if (isset($Parameters['state']))
            $command .= ' state="'.$Parameters['state'].'"';
        if (isset($Parameters['endState']))
            $command .= ' end_state="'.$Parameters['endState'].'"';
        if (isset($Parameters['replace']))
            $command .= ' replace="'.$Parameters['replace'].'"';

        if (isset($Parameters['parameters'])) {
            $command .= '><params>';
            foreach ($Parameters['parameters'] as $k => $Param) {
                foreach ($Param as $k=>$v) {
                    $command .= '<param name="'.$k.'" value="'.$v.'"/>';
                }
            }
            $command .= '</params></'.$type.'>';
        }
        else {
            $command .= '/>';
        }
        
        // Mode persistent
        if ($type=='order')
            $command = '<modify_hot_folder folder="'.$Parameters['path'].'">'.$command.'</modify_hot_folder>';

        $Data = $this->command($instance,$command);
        return $this->Answer($Data);
    }
    
    function deleteOrder($instance,$orderId,$Parameters) {
        list($chain,$order)=split(',',$orderId);
        $command = '<remove_order order="'.$order.'" job_chain="'.$Parameters['path'].'/'.$chain.'"/>';
            
        $Data = $this->command($instance,$command);
        return $this->Answer($Data);
    }
    
}
