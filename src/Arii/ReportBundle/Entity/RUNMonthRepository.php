<?php

namespace Arii\ReportBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * RUNMonthRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RUNMonthRepository extends EntityRepository
{

   public function findExecutionsByMonth($start,$end, $env='P', $app='*')
   {
       if ($app=='*') {
             return $this->createQueryBuilder('run')
                 ->Select('run.year,run.month,sum(run.warnings) as warnings,sum(run.alarms) as alarms,sum(run.acks) as acks,sum(run.executions) as executions')
                 ->where('(run.year*100+run.month) >= :start')
                 ->andWhere('(run.year*100+run.month) <= :end')
                 ->andWhere('run.env = :env')
                 ->groupBy('run.year,run.month')
                 ->orderBy('run.year,run.month')
                 ->setParameter('start', $start*1)
                 ->setParameter('end', $end*1)
                 ->setParameter('env', $env)
                 ->getQuery()
                 ->getResult();
       }
       else {
            return $this->createQueryBuilder('run')
                 ->Select('run.year,run.month,run.env,run.application,run.warnings,run.alarms,run.acks,run.executions')
                 ->where('(run.year*100+run.month) >= :start')
                 ->andWhere('(run.year*100+run.month) <= :end')
                 ->andWhere('run.env = :env')
                 ->andWhere('run.application = :app')
                 ->orderBy('run.year,run.month')
                 ->setParameter('start', $start*1)
                 ->setParameter('end', $end*1)
                 ->setParameter('env', $env)
                 ->setParameter('app', $app)
                 ->getQuery()
                 ->getResult();
       }
   }

   public function findApplicationsByMonths($start,$end, $env='P')
   {
        if ($env=='*') {
            $qb = $this->createQueryBuilder('run')
                ->Select('run.year,run.month,run.application,run.executions,run.alarms,run.warnings,run.acks')
                 ->where('(run.year*100+run.month) >= :start')
                 ->andWhere('(run.year*100+run.month) <= :end')
                ->orderBy('run.year,run.month,run.application,run.executions','DESC')
                ->setParameter('start', $start)
                ->setParameter('end', $end);
                /*
                        $query = $qb->getQuery();
                        print $query->getSQL();
                        print_r($query->getParameters());
                        exit();
                */
        }
        else {
            $qb = $this->createQueryBuilder('run')
                ->Select('run.year,run.month,run.application,run.executions,run.alarms,run.warnings,run.acks')
                 ->where('(run.year*100+run.month) >= :start')
                 ->andWhere('(run.year*100+run.month) <= :end')
                ->andWhere('run.env = :env')                        
                ->orderBy('run.year,run.month,run.application,run.executions','DESC')
                ->setParameter('start', $start)
                ->setParameter('end', $end)            
                ->setParameter('env', $env);
        }
        return $qb->getQuery()
                ->getResult();
   }
   
   public function findApplicationsExecutions($year,$month,$env='*')
   {
        if ($env=='*') {
            $qb = $this->createQueryBuilder('run')
                ->Select('run.executions','run.application','run.alarms','run.acks')
                ->where('run.year = :year')
                ->andWhere('run.month = :month')                        
                ->orderBy('run.executions','DESC')
                ->setParameter('year', $year)
                ->setParameter('month', $month);
                /*
                        $query = $qb->getQuery();
                        print $query->getSQL();
                        print_r($query->getParameters());
                        exit();
                */
        }
        else {
            $qb = $this->createQueryBuilder('run')
                ->Select('run.application','run.executions','run.alarms','run.acks')
                ->where('run.year = :year')
                ->andWhere('run.month = :month')                        
                ->andWhere('run.env = :env')                        
                ->orderBy('run.executions','DESC')
                ->setParameter('year', $year)
                ->setParameter('month', $month)            
                ->setParameter('env', $env);
        }
        return $qb->getQuery()
                ->getResult();
   }

   public function findApplicationsAlarms($year,$month,$env='*')
   {
        if ($env=='*') {
            return $this->createQueryBuilder('run')
                ->Select('run.application','app.title','run.executions','run.alarms','run.acks')
                ->leftJoin('Arii\CoreBundle\Entity\Application','app',\Doctrine\ORM\Query\Expr\Join::WITH,'run.application = app.name')                
                ->where('run.year = :year')
                ->andWhere('run.month = :month')                        
                ->orderBy('run.alarms','DESC')
                ->setParameter('year', $year)
                ->setParameter('month', $month)
                ->getQuery()
                ->getResult();
        }
        else {
            return $this->createQueryBuilder('run')
                ->Select('run.application','app.title','run.executions','run.alarms','run.acks')
                ->leftJoin('Arii\CoreBundle\Entity\Application','app',\Doctrine\ORM\Query\Expr\Join::WITH,'run.application = app.name')                
                ->where('run.year = :year')
                ->andWhere('run.month = :month')     
                ->andWhere('run.env = :env')     
                ->orderBy('run.alarms','DESC')
                ->setParameter('year', $year)
                ->setParameter('month', $month)
                ->setParameter('env', $env)
                ->getQuery()
                ->getResult();
        }
   }      
      
}