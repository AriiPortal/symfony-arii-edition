<?php

namespace Arii\ACKBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * StatusRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StatusRepository extends EntityRepository
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
        ->select('e.id,e.source,e.name,e.instance,e.title,e.status,e.state,e.status_time')
        ->where('e.state = :state')
        ->orderBy('e.status_time','DESC')
        ->setParameter('state',$state)
        ->getQuery();
        return $q->getResult();
    }

    public function Job($id) {
        
        $q = $this
        ->createQueryBuilder('e')
        ->select("e.id","e.name","e.title","e.description","e.status","e.state_time","e.status_time","e.job_log" )
        ->where('e.id = :id')
        ->setParameter('id', $id)
        ->getQuery();

        return $q->getResult();
    }    
}
