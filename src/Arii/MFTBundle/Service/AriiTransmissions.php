<?php
// src/Arii/JIDBundle/Service/AriiHistory.php
/*
 * Recupere les données et fournit un tableau pour les composants DHTMLx
 */ 
namespace Arii\MFTBundle\Service;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AriiTransmissions
{
    public function __construct (  
            \Arii\CoreBundle\Service\AriiPortal $portal ) {
    }

/*********************************************************************
 * Partners
 *********************************************************************/    
    public function Transmissions($em,$Filter=[]) {
        $Transmissions = $em->getRepository("AriiMFTBundle:Transmissions")->findTransmissions($Filter);
        return $Transmissions;        
    }
   
}
