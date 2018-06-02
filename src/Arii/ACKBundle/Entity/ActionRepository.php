<?php

namespace Arii\ACKBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ActionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ActionRepository extends EntityRepository
{
    public function getComments($event_id) {        
        $q = $this
        ->createQueryBuilder('e')
        ->select('e.id,e.name,e.title,e.user,e.date_time,(e.event)')
        ->where('e.event = :event')
        ->orderBy('e.date_time','DESC')
        ->setParameter('event', $event_id)
        ->getQuery();
        return $q->getResult();
    }    
}
