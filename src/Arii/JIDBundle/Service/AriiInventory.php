<?php
/*
 * Sous-couche appelée par les API et les contrôleurs
 * Recupere les infos sous forme de tableau
 * Full ORM
 */
namespace Arii\JIDBundle\Service;

class AriiInventory
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
        $Instances = $em->getRepository("AriiJIDBundle:InventoryInstances")->findInstances();
        foreach ($Instances as $k=>$Instance) {
        }
        return $Instances;
    }
}