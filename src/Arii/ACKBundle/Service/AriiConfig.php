<?php
namespace Arii\ACKBundle\Service;

class AriiConfig
{
    public function __construct ( ) {
    }

    public function Alerts($em,$Filter) {        
        $Alerts = $em->getRepository("AriiACKBundle:Alert")->findAlerts($Filter);
        return $Alerts;
    }
    
}
