<?php
// src/Arii/JOCBundle/Service/AriiState.php
/*
 * Recupere les données et fournit un tableau pour les composants DHTMLx
 */ 
namespace Arii\ATSBundle\Service;

use Symfony\Component\Translation\TranslatorInterface;

class AriiEvents
{
    protected $portal;
    protected $autosys;    
    protected $TZ;
    protected $offset;
    protected $translator;
    
    public function __construct (
            \Arii\CoreBundle\Service\AriiPortal $portal,
            \Arii\ATSBundle\Service\AriiAutosys $autosys,
            TranslatorInterface $translator) {

        date_default_timezone_set('UTC');
        $this->portal = $portal;
        $this->autosys = $autosys;
        $this->TZ = $portal->getTimeZone();
        $this->offset = timezone_offset_get( $this->TZ, new \DateTime() );
        $this->translator = $translator;
    }

    public function Events($em,$Filter) {      
        // Conversion du status em liste d'entier
        if (isset($Filter['eventType']))
            $Filter['eventType'] = $this->autosys->EventsToCodes($Filter['eventType']);
        if (isset($Filter['status']))
            $Filter['status'] = $this->autosys->StatusToCodes($Filter['status']);

        $Events = $em->getRepository("AriiATSBundle:UjoEvent")->findEvents($Filter);
        return $Events;
    }

    public function MachineEvents($em,$Filter) {      
        // Conversion du status em liste d'entier
        if (isset($Filter['eventType']) and ($Filter['eventType']!=''))
            $Filter['eventType'] = $this->autosys->EventsToCodes($Filter['eventType']);
        else 
            $Filter['eventType'] = $this->autosys->EventsToCodes('MACH_ONLINE,MACH_OFFLINE,INVALIDATE_MACH,MACH_PROVISION');

        if (isset($Filter['status']))
            $Filter['status'] = $this->autosys->StatusToCodes($Filter['status']);

        $Events = $em->getRepository("AriiATSBundle:UjoEvent")->findMachineEvents($Filter);
        foreach ($Events as $k=>$Event) {
            $Events[$k]['eventTimeGMT'] = new \DateTime('@'.$Event['eventTimeGmt']);
            $Events[$k]['eventTime'] = new \DateTime('@'.$Event['eventTimeGmt']);
            $Events[$k]['eventTime']->setTimezone($this->TZ);
            $Events[$k]['eventText'] = $this->autosys->Event($Events[$k]['event']);
            $Events[$k]['messageDisplay'] = $Event['machineName'].' '.$this->translator->trans($Events[$k]['eventText']);
            $Events[$k]['message'] = $Events[$k]['event'].' '.$Event['machineName'];
        }
        return $Events;
    }

}
