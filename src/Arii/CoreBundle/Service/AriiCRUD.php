<?php
/*
 * Gere les parametres passés en session et en requête
 */
namespace Arii\CoreBundle\Service;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

class AriiCRUD
{
    protected $em;
    
    public function __construct (EntityManager $em) {
        $this->em = $em;
    }

    /*********************************************************************
    * Applications
    *********************************************************************/
    public function Applications($Filter) {        
        // On se base sur l'historique
        $Applications = $this->em->getRepository("AriiCoreBundle:Application")->findApplications($Filter);
        return $Applications;
    }

}
