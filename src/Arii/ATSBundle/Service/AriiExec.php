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
}
?>
