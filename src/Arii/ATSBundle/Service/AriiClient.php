<?php
/*
 * Service d'execution Autosys
 */
namespace Arii\ATSBundle\Service;

class AriiClient {
    
    private $exec;
    private $clientPath;
    
    public function __construct(
            \Arii\CoreBundle\Service\AriiExec   $exec,
            $config
    ) {
        $this->clientPath = $config['client']['path'];
        $this->exec = $exec;
    }

    // Une execution autosys est toujours sur le serveur
    public function Exec($instance,$command) {
        // Construction de l'appel Windows
        $set = [
            'set AUTOSERV='.$instance,
            'set AUTOSYS='. $this->clientPath.'/autosys',
            'set AUTOUSER='.$this->clientPath.'/autouser.'.$instance,
            'set PATH=%PATH%;'.$this->clientPath.'/autosys/bin'
        ];
        $init = str_replace('/',DIRECTORY_SEPARATOR,implode('&&',$set));
        $command = $init."&&".$command;
        
        $descriptorspec = array(
            0 => array("pipe", "r"),  // // stdin est un pipe où le processus va lire
            1 => array("pipe", "w"),  // stdout est un pipe où le processus va écrire
            2 => array("pipe", "w") // stderr est un fichier
         );

        $process = proc_open($command, $descriptorspec, $pipes);
        if (is_resource($process)) {
            // fwrite($pipes[0], $digraph );
            // fclose($pipes[0]);

            $out = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $err = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            $return_value = proc_close($process);

            if ($return_value != 0) {
                $error = '';
                if ($out!='')
                    $error .= "STDOUT:\n$out<br/>"; 
                if ($err!='')
                    $error .= "STDERR:\n$err<br/>"; 
                $error .= "EXIT $return_value";
                return $error;
            }
        }  
        else {
            throw new \Exception("Ressource !");
        }
        return $out;
    }
    
    // Commande autorep 
    public function Autorep($Args) {
        $autorep = 'autorep';
        if (isset($Args['jobName']))
            $autorep .= ' -J '.$Args['jobName'];
        return $this->Exec($Args['instanceId'],$autorep);
    }

    public function Autorep_q($Args) {
        $autorep = 'autorep';
        if (isset($Args['jobName']))
            $autorep .= ' -J '.$Args['jobName'];
        $autorep .= ' -q';
        return $this->Exec($Args['instanceId'],$autorep);
    }

    public function Autorep_d($Args) {
        $autorep = 'autorep';
        if (isset($Args['jobName']))
            $autorep .= ' -J '.$Args['jobName'];
        $autorep .= ' -d';
        if (isset($Args['runNum'])) {
            $autorep .= ' -r '.$Args['runNum'];
        } 
        return $this->Exec($Args['instanceId'],$autorep);
    }

    public function Autotrack($Args) {
        $autotrack = 'autotrack -v';
        if (isset($Args['jobName']))
            $autotrack .= ' -J '.$Args['jobName'];
        return $this->Exec($Args['instanceId'],$autotrack);
    }
    
    public function Autosyslog($Args) {
        $autosyslog = 'autosyslog';
        if (isset($Args['jobName']))
            $autosyslog .= ' -J'.$Args['jobName'];
        if (isset($Args['logType']))
            $autosyslog .= ' -T'.substr($Args['logType'],0,1);
        if (isset($Args['runNum'])) {
            $autosyslog .= ' -r '.$Args['runNum'];
            if (($Args['runNum']>0) and isset($Args['nTry'])) {
                $autosyslog .= ' -n '.$Args['nTry'];
            } 
        } 
        return $this->Exec($Args['instanceId'],$autosyslog);
    }
    
}
?>
