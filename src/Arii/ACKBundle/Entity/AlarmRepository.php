<?php

namespace Arii\ACKBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * AlarmRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AlarmRepository extends EntityRepository
{
    public function getNb($state='OPEN') {
        return $this->createQueryBuilder('e')
            ->select('COUNT(e)')
            ->where('e.state = :state')
            ->setParameter('state', $state)
            ->getQuery()
            ->getSingleScalarResult();
    }
    
    public function listState($state) {        
        $q = $this
        ->createQueryBuilder('e')
        ->select('e.name')
        ->where('e.state = :state')
        ->orderBy('e.state_time','DESC')
        ->setParameter('state',$state)
        ->getQuery();
        
        return $q->getResult();
    }
    
}
