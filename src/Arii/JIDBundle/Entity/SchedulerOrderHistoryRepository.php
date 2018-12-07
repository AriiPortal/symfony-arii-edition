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
    public function findStates($start,$end,$max_result,$sort='last',$only_warning=true) { 
        $q = $this->createQueryBuilder('e')
        ->where('e.startTime >= :start')
        ->andWhere('e.endTime <= :end')
        ->orderBy('e.startTime')
        ->setParameter('start', $start)
        ->setParameter('end', $end);
        
        if ($only_warning)
            $q->andWhere("e.state like '!%'");
        
        switch ($sort) {
            case 'last':
                $q->orderBy('e.endTime');
        }
        return $q->setMaxResults($max_result)
                ->getQuery()
                ->getResult();
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
