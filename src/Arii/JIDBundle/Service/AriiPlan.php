<?php
// src/Arii/JIDBundle/Service/AriiPlan.php
/*
 * Sous-couche appelée par les API et les contrôleurs
 * Recupere les infos sous forme de tableau
 * Full ORM
 */
namespace Arii\JIDBundle\Service;

class AriiPlan
{
    protected $portal;
    protected $tools;
    protected $TZoffset;
    protected $TZinterval;
    
    public function __construct (
            \Arii\CoreBundle\Service\AriiPortal $portal,
            \Arii\CoreBundle\Service\AriiTools $tools ) {

        $this->portal = $portal;
        $this->tools = $tools;        
        
        # Recalage Doctrine
        $timezone = new \DateTimeZone(date_default_timezone_get());
        $this->TZoffset = $timezone->getOffset(new \DateTime()); 
    }
    
 /*********************************************************************
 * Orders
 *********************************************************************/
    public function Orders($em) {        
        // On se base sur l'historique
        $Orders = $em->getRepository("AriiJIDBundle:DailyPlan")->findOrders();
        foreach ($Orders as $k=>$Order) {
            $Orders[$k] = $this->getOrder($Order);            
        }
        return $Orders;
    }
    
}

