<?php

namespace Arii\ReportBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * RUNDayRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RUNHourRepository extends EntityRepository
{
   public function findExecutionsByHour($start, $end, $env='*', $app='*')
    {
       if ($env=='*') {
            return $this->createQueryBuilder('run')
                  ->Select('run.date,run.hour,run.env,sum(run.warnings) as warnings,sum(run.alarms) as alarms,sum(run.acks) as acks,sum(run.executions) as executions')
                  ->where('run.date >= :start')
                  ->andWhere('run.date < :end')
                  ->groupBy('run.date,run.hour,run.env')
                  ->orderBy('run.date,run.hour,run.env')
                  ->setParameter('start', $start)
                  ->setParameter('end', $end)
                  ->getQuery()
                  ->getResult();
       }
       else {
            return $this->createQueryBuilder('run')
                  ->Select('run.date,run.hour,sum(run.warnings) as warnings,sum(run.alarms) as alarms,sum(run.acks) as acks,sum(run.executions) as executions')
                  ->where('run.date >= :start')
                  ->andWhere('run.date < :end')
                  ->andWhere('run.env = :env')
                  ->groupBy('run.date,run.hour')
                  ->orderBy('run.date,run.hour')
                  ->setParameter('start', $start)
                  ->setParameter('end', $end)
                  ->setParameter('env', $env)
                  ->getQuery()
                  ->getResult();
       }
    }   

   public function findLast()
   {
        return $this->createQueryBuilder('run')
            ->Select('count(run) as NB,max(run.date) as lastUpdate')
            ->getQuery()
            ->getResult();
   }
   
}