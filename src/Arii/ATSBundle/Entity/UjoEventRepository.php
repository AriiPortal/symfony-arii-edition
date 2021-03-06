<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UjoEventRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UjoEventRepository extends EntityRepository
{
    
    public function findEvents($Filter) {    
        $q = $this
        ->createQueryBuilder('e')
        ->select('e.eoid as eventId,e.event,e.jobName,e.boxName,e.globalName,e.globalValue')
        ->orderBy('e.eoid')
        ->setMaxResults(100);    

        if (isset($Filter['eventType'])) {
            $q->andWhere('e.event in ( :eventType )')
            ->setParameter('eventType',$Filter['eventType']); 
        }
        if (isset($Filter['status'])) {
            $q->andWhere('e.status in ( :status )')
            ->setParameter('status',$Filter['status']); 
        }
        return $q->getQuery()
                ->getResult();
    }    

    public function findMachineEvents($Filter) {    
        $q = $this
        ->createQueryBuilder('e')
        ->select('e.autoserv,e.boxJoid,e.boxName,e.eoid,e.event,e.eventTimeGmt,e.evtNum,e.machName as machineName,e.stamp,e.text')
        ->orderBy('e.eoid')
        ->setMaxResults(100);    

        if (isset($Filter['eventType'])) {
            $q->andWhere('e.event in ( :eventType )')
            ->setParameter('eventType',$Filter['eventType']); 
        }
        if (isset($Filter['status'])) {
            $q->andWhere('e.status in ( :status )')
            ->setParameter('status',$Filter['status']); 
        }
        return $q->getQuery()
                ->getResult();
    }    
    
}
