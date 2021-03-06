<?php

namespace Arii\JIDBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * SchedulerOrderHistoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SchedulerOrderHistoryRepository extends EntityRepository
{
    public function findOrders($Filter) { 
        $q = $this->createQueryBuilder('e')
        ->select('e.history as id,e.spoolerId,e.orderId,e.jobChain,e.title,e.state,e.stateText,e.startTime,e.endTime')
        ->orderBy('e.history','desc')
        ->setMaxResults(1000);
        # Filtrage
        if (isset($Filter['limit']))
            $q->setMaxResults($Filter['limit']);
        if (isset($Filter['spooler_name']))
            $q->andWhere('e.spoolerId like :spooler_name')
                ->setParameter('spooler_name',$Filter['spooler_name']);           
        if (isset($Filter['state']))
            $q->andWhere('e.state like :state')
                ->setParameter('state',$Filter['state']);           
        return $q->getQuery()->getResult();
    }

    public function findLastErrors() { 
        $q = $this->createQueryBuilder('e')
        ->select('e.history as id,e.spoolerId,e.orderId,e.jobChain,e.title,e.state,e.stateText,e.startTime,e.endTime')
        ->orderBy('e.history','desc')
        ->where("e.state like '!%'")
        ->orderBy('e.endTime','desc')
        ->setMaxResults(100)
        ->getQuery();
        return $q->getResult();
    }
    
    public function findOrder($orderId) { 
        $q = $this->createQueryBuilder('e')
        ->select('e.spoolerId,e.orderId,e.jobChain,e.title,e.state,e.stateText,e.startTime,e.endTime')
        ->where('e.history = :id')
        ->setParameter('id',$orderId)
        ->getQuery();
        return $q->getResult();
    }
    
    public function findHistory($from,$to,$sort='DESC',$limit=100)
    {
        $q = $this->createQueryBuilder('o')
            ->Select(
                    'a.id as taskId,a.spoolerId,a.clusterMemberId,a.jobName,a.startTime,a.endTime,a.cause,a.steps,a.exitCode,a.error,a.errorCode,a.errorText,a.parameters,a.log,a.pid,a.agentUrl,
                    s.step,
                    o.jobChain,o.spoolerId,o.state,o.stateText,o.orderId')
            ->leftjoin('AriiJIDBundle:SchedulerOrderStepHistory','s',\Doctrine\ORM\Query\Expr\Join::WITH,'o.history = s.task')
            ->leftjoin('AriiJIDBundle:SchedulerHistory','a',\Doctrine\ORM\Query\Expr\Join::WITH,'s.task = a.id')                
            ->Where('a.endTime <= :endTime')
            ->orderBy('a.startTime',$sort)
            ->setParameter('endTime', $to)
            ->setMaxResults($limit);
        if ($from != null) {
            $q->andWhere('a.startTime >= :startTime')
            ->setParameter('startTime', $from);
        }    
        return $q->getQuery()
                ->getResult();
    }
    
   public function findSpoolers()
   {
        return $this->createQueryBuilder('a')
            ->Select('a.spoolerId')
            ->distinct('a.spoolerId')                
            ->orderBy('a.spoolerId')
            ->getQuery()
            ->getResult();
   }    
    
    // Liste des spoolers
    public function listSpoolers($start) {        
        $q = $this
        ->createQueryBuilder('e')
        ->select('e.spoolerId')
        ->where('e.startTime >= :start')
        ->distinct('e.spoolerId')
        ->setParameter('start', $start)
        ->getQuery();
        return $q->getResult();
    }
    
    // Pour la synchronisation des historique
    public function findStates($start,$end,$maxResult,$sort='last',$onlyWarning=true) { 
        $q = $this->createQueryBuilder('e')
        ->where('e.startTime >= :start')
        ->andWhere('e.endTime <= :end')
        ->orderBy('e.startTime')
        ->setParameter('start', $start)
        ->setParameter('end', $end);
        
        if ($onlyWarning)
            $q->andWhere("e.state like '!%'");
        
        switch ($sort) {
            case 'last':
                $q->orderBy('e.endTime');
        }
        return $q->setMaxResults($maxResult)
                ->getQuery()
                ->getResult();
    }

    
    // Les ordres les plus vieux
    public function findLatestOrders($endtime,$limit=100)
    {
        $q = $this->createQueryBuilder('o')
            ->Select('o.history,o.jobChain,o.spoolerId,o.state,o.stateText,o.orderId,o.title,o.startTime,o.endTime')
            ->Where('o.endTime <= :endTime')
            ->orderBy('o.history')
            ->setParameter('endTime', $endtime)
            ->setMaxResults($limit);
        return $q->getQuery()
                ->getResult();
    }
    
    public function findLatestOrdersOLD($endtime,$limit=1000) { 
        return $this->createQueryBuilder('e')
        ->select()
        ->where('e.endTime < :endtime')
        ->setParameter('endtime', $endtime)
        ->orderBy('e.history')
        ->setMaxResults($limit)
        ->getQuery()
        ->getResult();
    }
    
    // Crashs database
    public function findOrdersWithoutSteps($limit=1000) {
        $q = $this->createQueryBuilder('o')
        ->select('o')
        ->leftjoin('AriiJIDBundle:SchedulerOrderStepHistory','s',\Doctrine\ORM\Query\Expr\Join::WITH,'o.history = s.history')
        ->where('s.history is null')
        ->orderBy('o.history')
        ->setMaxResults($limit)              
        ->getQuery();
        return $q->getResult();
    }
    
    // Pour la synchronisation des acquittements
    // 4 jours en arriere
    public function synchroOrderHistory() {        
        $q = $this
        ->createQueryBuilder('e')
        ->select('e.jobName,e.description,e.jobType,e.runNum,e.ntry,e.lastStart,e.lastEnd,e.exitCode,e.runMachine,e.status')
        ->getQuery();
        return $q->getResult();
    }
    
    // Pour la synchronisation des historique
    public function synchroHistory($last_id) { 
        $q = $this->createQueryBuilder('e')
        ->where('(e.history) > :id')
        ->orderBy('e.startTime','DESC')
        ->setParameter('id',$last_id)
        ->getQuery();
        return $q->getResult();
    }

}
