<?php

namespace Arii\JIDBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * SchedulerHistoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SchedulerHistoryRepository extends EntityRepository
{
    // Pour la synchronisation des historique
    public function synchroHistory($past) { 
        $driver = $this->_em->getConnection()->getDriver()->getName();
        if ($driver=='oci8') {
            $dbh = $this->_em->getConnection();
            $sth = $dbh->prepare("ALTER SESSION SET NLS_DATE_FORMAT='YYYY-MM-DD HH24:MI:SS'");
            $sth->execute();
        }
        
        $q = $this->createQueryBuilder('e')
        ->select('e.jobName,e.steps,e.startTime,e.endTime,e.exitCode,e.spoolerId,e.error,e.errorCode,e.errorText')
        ->where('e.startTime > :past')
        ->orderBy('e.startTime','DESC')
        ->setParameter('past',$past)
        ->getQuery();
        return $q->getResult();
    }
    
}
