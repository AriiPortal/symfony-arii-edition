<?php

namespace Arii\ACKBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * EventRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EventRepository extends EntityRepository
{
    public function listOK() {        
        $q = $this
        ->createQueryBuilder('e')
        ->select('e.id,e.name,e.title,e.status,e.state,e.end_time')
        ->where('e.state > 127')
        ->orderBy('e.end_time','DESC')
        ->getQuery();
        return $q->getResult();
    }
    
    public function listNotOK() {        
        $q = $this
        ->createQueryBuilder('e')
        ->select('e.id,e.name,e.title,e.status,e.state,e.end_time')
        ->where('e.state < 128')
        ->orderBy('e.end_time','DESC')
        ->getQuery();
        return $q->getResult();
    }
    
    public function Event($id) {
        
        $q = $this
        ->createQueryBuilder('e')
        ->select("e.id","e.name","e.title","e.description","e.event","e.event_type","e.start_time","e.end_time","(e.application) as application_id","(e.domain) as domain_id")
        ->where('e.id = :id')
        ->setParameter('id', $id)
        ->getQuery();

        return $q->getResult();
    }

    public function findEvents($start,$end) {
        
        $q = $this
        ->createQueryBuilder('e')
        ->select("e.id","e.name","e.title","e.description","e.event","e.event_type","e.start_time","e.end_time","(e.application) as application_id","(e.domain) as domain_id")
        ->where('e.start_time >= :start')
        ->andWhere('e.end_time <= :end')
        ->setParameter('start', $start)
        ->setParameter('end', $end)
        ->getQuery();

        return $q->getResult();
    }
    
    public function findEventsPerHour($from,$to) {
        
        $q = $this
        ->createQueryBuilder('e')
        ->select('e.id','e.name','e.title','e.description','e.event',"e.event_type",'e.start_time','e.end_time',"(e.application) as application_id","(e.domain) as domain_id")
        ->where('e.start_time >= :from')
        ->andWhere('e.end_time <= :to')
        ->setParameter('from', $from)
        ->setParameter('to', $to)
        ->getQuery();

        return $q->getResult();
    }
    
}
