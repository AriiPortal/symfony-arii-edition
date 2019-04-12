<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UjoAlarmRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UjoAlarmRepository extends EntityRepository
{
    // Pour la synchronisation des historique
    public function findOpenIssues() { 
        $q = $this->createQueryBuilder('e')
        ->select('e.eoid as id,e.joid,e.alarm,e.alarmTime,e.state,e.stateTime,e.response,e.theUser,e.eventComment,j.jobName,j.description,j.status')
        ->leftjoin('AriiATSBundle:UjoJobst','j',\Doctrine\ORM\Query\Expr\Join::WITH,'e.joid = j.joid')
        ->where('e.state < 45')
        ->orderBy('e.eoid','desc')
        ->setMaxResults(1000)
        ->getQuery();
        return $q->getResult();
    }
    
    public function findAlarm($eoid) { 
        $q = $this->createQueryBuilder('e')
        ->select('e.eoid as id,e.joid,e.alarm,e.alarmTime,e.state,e.stateTime,e.response,e.theUser,e.eventComment,j.jobName,j.description,j.status')
        ->leftjoin('AriiATSBundle:UjoJobst','j',\Doctrine\ORM\Query\Expr\Join::WITH,'e.joid = j.joid')
        ->where('e.eoid = :eoid')
        ->setParameter('eoid',$eoid)
        ->setMaxResults(1)
        ->getQuery();
        return $q->getResult();
    }
    
}
