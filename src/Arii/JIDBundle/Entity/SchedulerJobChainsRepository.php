<?php

namespace Arii\JIDBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * SchedulerJobChainsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SchedulerJobChainsRepository extends EntityRepository
{
    public function findChains($Filter=[]) { 
        $q = $this->createQueryBuilder('e')
        ->select('e.spoolerId as spoolerName,e.clusterMemberId,e.path as Chain,e.stopped as isStopped')
        ->setMaxResults(1000);
        # Filtrage
        if (isset($Filter['limit']))
            $q->setMaxResults($Filter['limit']);
        if (isset($Filter['isStopped']))
            $q->andWhere("e.stopped = ".($Filter['isStopped']=='true'?1:0));
        return $q->getQuery()->getResult();
    }
       
}
