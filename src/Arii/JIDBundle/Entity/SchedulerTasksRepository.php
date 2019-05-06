<?php

namespace Arii\JIDBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * SchedulerTasksRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SchedulerTasksRepository extends EntityRepository
{   
    public function findTasks() { 
        $q = $this->createQueryBuilder('e')
        ->select('e.spoolerId,e.taskId,e.clusterMemberId,e.jobName,e.enqueueTime,e.startAtTime,e.parameters,e.taskXml')
        ->orderBy('e.taskId','desc')
        ->setMaxResults(1000)
        ->getQuery();
        return $q->getResult();
    }
}