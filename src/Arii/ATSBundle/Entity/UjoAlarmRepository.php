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
    public function findAlarms($Filter=[]) { 
        $q = $this->createQueryBuilder('a')
        ->select('a.eoid,a.joid,a.alarm,a.alarmTime,a.state,a.stateTime,a.response,a.theUser,a.eventComment,'
                . 'j.jobName,j.description,j.status,'
                . 'e.runNum,e.ntry,e.machName')
        ->leftjoin('AriiATSBundle:UjoJobst','j',\Doctrine\ORM\Query\Expr\Join::WITH,'a.joid = j.joid')
        ->leftjoin('AriiATSBundle:UjoProcEvent','e',\Doctrine\ORM\Query\Expr\Join::WITH,'a.eoid = e.eoid');
        if (isset($Filter['jobName'])) {
            $q->andWhere('j.jobName = :jobName')
            ->setParameter('jobName',$Filter['jobName']);
        }          
        // A la minute
        if (isset($Filter['alarmTime'])) {
            $q->andWhere('a.alarmTime >= :alarmTime')
            ->andWhere('a.alarmTime <= :alarmTime2')        
            ->setParameter('alarmTime',$Filter['alarmTime']-> format('U'))
            ->setParameter('alarmTime2',$Filter['alarmTime']-> format('U')+60);
        }          
        if (isset($Filter['state']) and ($Filter['state']>0)) {
            $q->andWhere('a.state = :state')
            ->setParameter('state',$Filter['state']);
        }          
        return $q->orderBy('a.eoid','desc')
            ->setMaxResults(1000)
            ->getQuery()
            ->getResult();
    }

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
