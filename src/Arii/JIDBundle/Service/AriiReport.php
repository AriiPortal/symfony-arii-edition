<?php
// src/Arii/JIDBundle/Service/AriiAPI.php
/*
 * Sous-couche appelée par les API et les contrôleurs
 * Recupere les infos sous forme de tableau
 * Full ORM
 */
namespace Arii\JIDBundle\Service;

class AriiReport
{
    protected $portal;
    protected $tools;

    public function __construct (
            \Arii\CoreBundle\Service\AriiPortal $portal,
            \Arii\CoreBundle\Service\AriiTools $tools ) {

        $this->portal = $portal;
        $this->tools = $tools;        
    }
    
/*********************************************************************
 * Instances
 *********************************************************************/
    public function Instances($em) {

        // On se base sur l'historique
        $Instances = $em->getRepository("AriiJIDBundle:SchedulerInstances")->findInstances();
        foreach ($Instances as $k=>$Instance) {
            // Suppression des connexions
            $db = $Instance['dbName'];
            $db = str_replace('jdbc -class=','',$db);
            $db = substr($db,0,strpos($db,' -user='));
            $Instances[$k]['dbName'] = $db;
        }
        return $Instances;
    }
    
}

