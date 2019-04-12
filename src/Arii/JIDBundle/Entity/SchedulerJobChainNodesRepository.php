<?php

namespace Arii\JIDBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * SchedulerJobChainNodesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SchedulerJobChainNodesRepository extends EntityRepository
{
       
    // Pour la synchronisation des historique
    public function findStopped() { 
        $q = $this->createQueryBuilder('e')
        ->select('e')
        ->where('e.action is not null')
        ->getQuery();
        return $q->getResult();
    }
    
}