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
   // Liste des applications 
   public function findApps($year,$month,$env='*',$class='*')
   {
        $Filter = [ 'run.app' ];
/* Pour le detail
        if ($env=='*')
            array_push($Filter,'run.env');
        if ($class=='*')
            array_push($Filter,'run.jobClass');
*/        
        $f = implode(',',$Filter);
        $qb = $this->createQueryBuilder('run')
             ->Select($f.',sum(run.executions) as runs,sum(run.warnings) as warnings,sum(run.alarms) as alarms,sum(run.acks) as acks')
             ->where('run.year = :year')
             ->andWhere('run.month = :month')                
             ->groupBy($f)
             ->orderBy('run.app','ASC')
             ->setParameter('year', $year)
             ->setParameter('month', $month);
        
        if ($env!='*')
            $qb->andWhere('run.env = :env')
                 ->setParameter('env', $env);        
        if ($class!='*')
            $qb->andWhere('run.jobClass = :class')
                 ->setParameter('class', $class);
        
        return $qb->getQuery()
             ->getResult();
   }   
    
    
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
                 ->Select('run.year,run.month,run.env,run.app,run.warnings,run.alarms,run.acks,run.executions')
                 ->where('(run.year*100+run.month) >= :start')
                 ->andWhere('(run.year*100+run.month) <= :end')
                 ->andWhere('run.env = :env')
                 ->andWhere('run.app = :app')
                 ->orderBy('run.year,run.month')
                 ->setParameter('start', $start*1)
                 ->setParameter('end', $end*1)
                 ->setParameter('env', $env)
                 ->setParameter('app', $app)
                 ->getQuery()
                 ->getResult();
       }
   }

   public function findAppsByMonths($start,$end, $env='P')
   {
        if ($env=='*') {
            $qb = $this->createQueryBuilder('run')
                ->Select('run.year,run.month,run.app,run.executions,run.alarms,run.warnings,run.acks')
                 ->where('(run.year*100+run.month) >= :start')
                 ->andWhere('(run.year*100+run.month) <= :end')
                ->orderBy('run.year,run.month,run.app,run.executions','DESC')
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
                ->Select('run.year,run.month,run.app,run.executions,run.alarms,run.warnings,run.acks')
                 ->where('(run.year*100+run.month) >= :start')
                 ->andWhere('(run.year*100+run.month) <= :end')
                ->andWhere('run.env = :env')                        
                ->orderBy('run.year,run.month,run.app,run.executions','DESC')
                ->setParameter('start', $start)
                ->setParameter('end', $end)            
                ->setParameter('env', $env);
        }
        return $qb->getQuery()
                ->getResult();
   }
   
   public function findAppsExecutions($year,$month,$env='*')
   {
        if ($env=='*') {
            $qb = $this->createQueryBuilder('run')
                ->Select('run.executions','run.app','run.alarms','run.acks')
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
                ->Select('run.app','run.executions','run.alarms','run.acks')
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

   public function findAppsAlarms($year,$month,$env='*')
   {
        if ($env=='*') {
            return $this->createQueryBuilder('run')
                ->Select('run.app','app.title','run.executions','run.alarms','run.acks')
                ->leftJoin('Arii\CoreBundle\Entity\App','app',\Doctrine\ORM\Query\Expr\Join::WITH,'run.app = app.name')                
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
                ->Select('run.app','app.title','run.executions','run.alarms','run.acks')
                ->leftJoin('Arii\CoreBundle\Entity\App','app',\Doctrine\ORM\Query\Expr\Join::WITH,'run.app = app.name')                
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