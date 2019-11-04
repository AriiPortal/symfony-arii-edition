<?php
// src/Arii/JOCBundle/Service/AriiState.php
/*
 * Recupere les données et fournit un tableau pour les composants DHTMLx
 */ 
namespace Arii\ATSBundle\Service;

class AriiStatus
{
    protected $portal;
    protected $autosys;    
    protected $TZ;
    protected $offset;

    public function __construct (
            \Arii\CoreBundle\Service\AriiPortal $portal,
            \Arii\ATSBundle\Service\AriiAutosys $autosys ) {

        date_default_timezone_set('UTC');
        $this->portal = $portal;
        $this->autosys = $autosys;
        $this->TZ = $portal->getTimeZone();
        $this->offset = timezone_offset_get( $this->TZ, new \DateTime() );
    }

    public function Status($em,$Filter) {
        // Conversion du status em liste d'entier
        if (isset($Filter['status']))
            $Filter['status'] = $this->autosys->StatusToCodes(explode('|',$Filter['status']));

        $Status = $em->getRepository("AriiATSBundle:UjoJobst")->findStatus($Filter);
        foreach ($Status as $k=>$Jobst) {
            $Status[$k]['status'] = $this->autosys->Status($Jobst['status']);
            list($Status[$k]['color'])  = $this->autosys->ColorStatus($Status[$k]['status']);
            if ($Status[$k]['status']=='SUCCESS') 
                $Status[$k]['success']=1;
            else 
                $Status[$k]['success']=0;
            
            $Status[$k]['lastStart'] = new \DateTime('@'.$Jobst['lastStart']);
            $Status[$k]['lastStart']->setTimezone($this->TZ);
            $Status[$k]['lastEnd'] = new \DateTime('@'.$Jobst['lastEnd']);
            $Status[$k]['lastEnd']->setTimezone($this->TZ);
            $Status[$k]['nextStart'] = new \DateTime('@'.$Jobst['nextStart']);
            $Status[$k]['nextStart']->setTimezone($this->TZ);
            // peut etre interessant de conserver une heure GMT quelque part 
        }
        return $Status;
    }
    
}