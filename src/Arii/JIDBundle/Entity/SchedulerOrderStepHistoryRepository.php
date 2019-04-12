<?php

namespace Arii\JIDBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * SchedulerOrderStepRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SchedulerOrderStepHistoryRepository extends EntityRepository
{   
    public function findOrderSteps($id) {
        $q = $this->createQueryBuilder('e')
        ->select('IDENTITY(e.task) as id,e.step,e.state,e.startTime,e.endTime,e.error,e.errorCode,e.errorText')
        ->where('e.history = :id')
        ->setParameter('id',$id)
        ->orderBy('e.step')
        ->getQuery();
        return $q->getResult();
    }
    
    public function firstId() {
        $q = $this->createQueryBuilder('s')
        ->select('min(s.task)')
        ->getQuery();
        return $q->getSingleScalarResult();
    }

    public function lastId() {
        $q = $this->createQueryBuilder('s')
        ->select('max(s.task)')
        ->getQuery();
        return $q->getSingleScalarResult();
    }
    
    public function synchro($id,$limit) {
        $q = $this->createQueryBuilder('s')
        ->select('(s.history) as idOrder,(s.task) as idHistory,s.step,s.state,s.error,s.errorCode,s.errorText')
        ->where('(s.task) > :last')
        ->andWhere('(s.task) <= :limit')
        ->orderBy('s.task')
        ->setParameter('last', $id)
        ->setParameter('limit', $id+$limit)
        ->getQuery();
//        print $q->getSQL();
        return $q->getResult();
    }

    // Pour la synchronisation des historique
    // TROP GOURMAND
    public function synchroOrderSteps($id,$limit) { 
        $q = $this->createQueryBuilder('s')
        ->select('s')
        ->select('o.jobChain,o.orderId,o.spoolerId,o.title,o.state,o.stateText,o.startTime,o.endTime,o.log,'
                . 's.step,s.state,s.startTime as step_start_time,s.endTime as step_end_time,s.error,s.errorCode,s.errorText,'
                . '(s.history) as idOrder,(s.task) as idHistory,'
                . 'h.clusterMemberId,h.jobName,h.exitCode,h.parameters,h.log as job_log,h.pid')
        ->leftjoin('AriiJIDBundle:SchedulerOrderHistory','o',\Doctrine\ORM\Query\Expr\Join::WITH,'s.history = o.history')
        ->leftjoin('AriiJIDBundle:SchedulerHistory','h',\Doctrine\ORM\Query\Expr\Join::WITH,'s.task = h.id')
        ->where('s.task > :id')
        ->orderBy('s.task')
        ->setParameter('id', $id)
        ->setMaxResults($limit)
        ->getQuery();
        return $q->getResult();
    }
    
}
