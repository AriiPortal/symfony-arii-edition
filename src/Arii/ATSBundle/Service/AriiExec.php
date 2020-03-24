<?php
/*
 * Service d'execution Autosys
 */
namespace Arii\ATSBundle\Service;

class AriiExec {
    
    protected $exec;
    
    public function __construct(
            \Arii\CoreBundle\Service\AriiExec   $exec
    ) {
        $this->exec = $exec;
    }
    
    // Une execution autosys est toujours sur le serveur
    public function Exec($em,$command,$stdin='') {
        // on retrouve la machine ?
        $Alamode = $em->getRepository("AriiATSBundle:UjoAlamode")->findOneBy(['type' => 'event_demon']);
        $event_demon = $Alamode->getStrVal();
        
        // On execute la commande sur le node
        list($exit,$out) = $this->exec->ExecNodeName($event_demon,$command);
        return $out;
    }
    
    // Commande autorep 
    public function Autorep($em,$Args) {
        $autorep = 'autorep';
        if (isset($Args['jobName'])) {
            $autorep = ' -J '.$Args['jobName'];
        }
        return $this->Exec($em,$autorep);
    }

    public function Autorep_q($em,$Args) {
        $autorep = 'autorep';
        if (isset($Args['jobName'])) {
            $autorep = ' -J '.$Args['jobName'];
        }
        $autorep = ' -q';
        return $this->Exec($em,$autorep);
    }

    public function Autorep_d($em,$Args) {
        $autorep = 'autorep';
        if (isset($Args['jobName'])) {
            $autorep = ' -J '.$Args['jobName'];
        }
        $autorep = ' -d';
        return $this->Exec($em,$autorep);
    }

    public function Autotrack($em,$Args) {
        $autotrack = 'autotrack';
        if (isset($Args['jobName'])) {
            $autotrack = ' -J '.$Args['jobName'];
        }
        return $this->Exec($em,$autorep);
    }
    
    public function Autosyslog($em,$Args) {
        $autosyslog = 'autosyslog';
        if (isset($Args['jobName'])) {
            $autosyslog .= ' -J'.$Args['jobName'];
        }
        if (isset($Args['logType'])) {
            $autosyslog .= ' -T'.substr($Args['logType'],0,1);
        }
        return `c:\arii\init.cmd&&$autosyslog`;
        return `$autosyslog`;
    }

}
?>
