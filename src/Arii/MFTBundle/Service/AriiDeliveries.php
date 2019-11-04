<?php
/*
 * Recupere les données et fournit un tableau pour les composants DHTMLx
 */ 
namespace Arii\MFTBundle\Service;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AriiDeliveries
{
    public function __construct (  
            \Arii\CoreBundle\Service\AriiPortal $portal ) {
    }

    public function Deliveries($em,$Filter) {       
        $Deliveries = $em->getRepository("AriiMFTBundle:Deliveries")->findDeliveries($Filter);        
        return $Deliveries;
    }
    
    public function Flows($em,$Filter) {       
        $Flows = $em->getRepository("AriiMFTBundle:Deliveries")->findFlows($Filter);
        // Mise en forme
        foreach ($Flows as $k=>$Flow) {
            foreach (['files_size_avg','files_count_avg','success_avg','skipped_avg','failed_avg'] as $f)
                $Flows[$k][$f] = round($Flow[$f]);
            // conversion en entier
            foreach (['runs',
                'files_size_sum','files_count_sum','success_sum','skipped_sum','failed_sum',
                'files_size_min','files_count_min','success_min','skipped_min','failed_min',
                'files_size_max','files_count_max','success_max','skipped_max','failed_max'
                ] as $f)
                $Flows[$k][$f] = (int) $Flow[$f];            
            // periode
            $Flows[$k]['days'] = (int) substr($Flow['days'],1,9);
            $Flows[$k]['frequency'] = round($Flows[$k]['days']/$Flows[$k]['runs']);
        }
        return $Flows;
    }
    
}
