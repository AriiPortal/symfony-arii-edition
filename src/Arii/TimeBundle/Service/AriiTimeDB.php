<?php
// src/Arii/TimeBundle/Service/AriiTimecode.php
/*
 * Connexion avec la DB
 */ 
namespace Arii\TimeBundle\Service;

class AriiTimeDB
{
    public function __construct (\Arii\CoreBundle\Service\AriiPortal $portal) {
    }
 
/*********************************************************************
 * Rules
 *********************************************************************/
    public function Rules($em) {

        // On se base sur l'historique
        $Rules = $em->getRepository("AriiTimeBundle:Rules")->findRules();
        return $Rules;
    }

/*********************************************************************
 * Zones
 *********************************************************************/
    public function Zones($em) {

        // On se base sur l'historique
        $Zones = $em->getRepository("AriiTimeBundle:Zones")->findZones();
        return $Zones;
    }

}
