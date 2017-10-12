<?php

namespace Arii\ReportBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * JOBDayRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class JOBDayRepository extends EntityRepository
{

   public function findJobsByDay($start, $end, $env='P', $app='*')
   {
       if ($app=='*') {
            return $this->createQueryBuilder('job')
                 ->Select('job.date,sum(job.jobs) as jobs,sum(job.created) as created,sum(job.deleted) as deleted')
                 ->where('job.date >= :start')
                 ->andWhere('job.date <= :end')
                 ->andWhere('job.env = :env')
                 ->groupBy('job.date')
                 ->orderBy('job.date')
                 ->setParameter('start', $start)
                 ->setParameter('end', $end)
                 ->setParameter('env', $env)
                 ->getQuery()
                 ->getResult();
       }
       else {
            return $this->createQueryBuilder('job')
                 ->Select('job.date,job.application,job.jobs,job.created,job.deleted')
                 ->where('job.date >= :start')
                 ->andWhere('job.date <= :end')
                 ->andWhere('job.env = :env')
                 ->andWhere('job.application = :app')
                 ->orderBy('job.date')
                 ->setParameter('start', $start)
                 ->setParameter('end', $end)
                 ->setParameter('env', $env)
                 ->setParameter('app', $app)
                 ->getQuery()
                 ->getResult();
       }
   }
    
   public function findJobsMonth()
   {
        $driver = $this->_em->getConnection()->getDriver()->getName();
        switch ($driver) {
            case 'oci8':
                $sql = "SELECT EXTRACT(YEAR FROM job.job_date) as job_year,EXTRACT(MONTH FROM job.job_date) as job_month,job.application,job.env,job.spooler_type,job.spooler_name,max(job.jobs) as jobs,max(job.created) as created,max(job.deleted) as deleted
                        FROM REPORT_JOB_DAY job
                        GROUP BY EXTRACT(YEAR FROM job.job_date),EXTRACT(MONTH FROM job.job_date),job.application,job.env,job.spooler_type,job.spooler_name";

                $rsm = new ResultSetMapping();
                $rsm->addScalarResult('JOB_YEAR', 'year');
                $rsm->addScalarResult('JOB_MONTH', 'month');
                $rsm->addScalarResult('APPLICATION', 'application');
                $rsm->addScalarResult('ENV', 'env');
                $rsm->addScalarResult('SPOOLER_TYPE', 'spooler_type');
                $rsm->addScalarResult('SPOOLER_NAME', 'spooler_name');
                $rsm->addScalarResult('JOBS', 'jobs');
                $rsm->addScalarResult('CREATED', 'created');
                $rsm->addScalarResult('DELETED', 'deleted');
                $query = $this->_em->createNativeQuery($sql, $rsm);
                return $query->getResult();         
                break;
            default:
                return $this->createQueryBuilder('job')
                      ->Select('Year(job.date) as year,Month(job.date) as month,job.application,job.env,job.spooler_type,job.spooler_name,max(job.jobs) as jobs,max(job.created) as created,max(job.deleted) as deleted')
                      ->groupBy('year,month,job.application,job.env,job.spooler_type,job.spooler_name')
                      ->getQuery()
                      ->getResult();
                break;
        }       
   }
   
   public function findJobsByMonth(\DateTime $start, \DateTime $end, $env='P', $app='*')
   {
       if (($app=='*') and ($env=='*')) {
           return $this->createQueryBuilder('job')
                 ->Select('Year(job.date) as annee,Month(job.date) as mois,job.application,job.env,max(job.jobs) as jobs,min(job.jobs) as minjobs,sum(job.created) as created,sum(job.deleted) as deleted')
                 ->where('job.date >= :start')
                 ->andWhere('job.date <= :end')
                 ->groupBy('annee,mois')
                 ->setParameter('start', $start)
                 ->setParameter('end', $end)
                 ->getQuery()
                 ->getResult();
       }
       elseif ($app=='*') {
            return $this->createQueryBuilder('job')
                 ->Select('Year(job.date) as annee,Month(job.date) as mois,job.application,job.env,max(job.jobs) as jobs,min(job.jobs) as minjobs,sum(job.created) as created,sum(job.deleted) as deleted')
                 ->where('job.date >= :start')
                 ->andWhere('job.date <= :end')
                 ->andWhere('job.env = :env')
                 ->groupBy('annee,mois,job.env')
                 ->setParameter('start', $start)
                 ->setParameter('end', $end)
                 ->setParameter('env', $env)
                 ->getQuery()
                 ->getResult();
       }
       elseif ($env=='*') {
            return $this->createQueryBuilder('job')
                 ->Select('Year(job.date) as annee,Month(job.date) as mois,job.application,job.env,max(job.jobs) as jobs,min(job.jobs) as minjobs,sum(job.created) as created,sum(job.deleted) as deleted')
                 ->where('job.date >= :start')
                 ->andWhere('job.date <= :end')
                 ->andWhere('job.application = :app')
                 ->groupBy('annee,mois,job.application')
                 ->setParameter('start', $start)
                 ->setParameter('end', $end)
                 ->setParameter('app', $app)
                 ->getQuery()
                 ->getResult();
       }
       else {
            return $this->createQueryBuilder('job')
                 ->Select('Year(job.date) as annee,Month(job.date) as mois,job.application,job.env,max(job.jobs) as jobs,min(job.jobs) as minjobs,sum(job.created) as created,sum(job.deleted) as deleted')
                 ->where('job.date >= :start')
                 ->andWhere('job.date <= :end')
                 ->andWhere('job.env = :env')
                 ->andWhere('job.application = :app')
                 ->groupBy('annee,mois')
                 ->setParameter('start', $start)
                 ->setParameter('end', $end)
                 ->setParameter('env', $env)
                 ->setParameter('app', $app)
                 ->getQuery()
                 ->getResult();
       }
   }

   public function findApplications($start,$end,$env='*')
   {
       if (($env=='*') or ($env=='')) {
            return $this->createQueryBuilder('job')
                ->Select('job.application','app.title','max(job.jobs) as jobs')
                ->leftJoin('Arii\CoreBundle\Entity\Application','app',\Doctrine\ORM\Query\Expr\Join::WITH,'job.application = app.name')                
                ->where('job.date >= :start')
                ->andWhere('job.date <= :end')                        
                ->groupBy('job.application,app.title')
                ->orderBy('jobs','DESC')
                ->setParameter('start', $start)
                ->setParameter('end', $end)
                ->getQuery()
                ->getResult();
       }
       else {
            return $this->createQueryBuilder('job')
                ->Select('job.application','app.title','max(job.jobs) as jobs')
                ->leftJoin('Arii\CoreBundle\Entity\Application','app',\Doctrine\ORM\Query\Expr\Join::WITH,'job.application = app.name')                
                ->where('job.date >= :start')
                ->andWhere('job.date <= :end')                        
                ->andWhere('job.env = :env')
                ->groupBy('job.application,app.title')
                ->orderBy('jobs','DESC')
                ->setParameter('start', $start)
                ->setParameter('end', $end)
                ->setParameter('env', $env)
                ->getQuery()
                ->getResult();
       }
   }

   public function findLast()
   {
        return $this->createQueryBuilder('job')
            ->Select('count(job) as NB,max(job.date) as lastUpdate')
            ->getQuery()
            ->getResult();
   }
   
}