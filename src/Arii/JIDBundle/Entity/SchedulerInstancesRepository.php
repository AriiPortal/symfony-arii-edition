<?php

namespace Arii\JIDBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * SchedulerInstancesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SchedulerInstancesRepository extends EntityRepository
{

   public function findInstances($Filter)
   {
        $q = $this->createQueryBuilder('e')
            ->select('e.id,e.schedulerId as spoolerName,e.hostname,e.tcpPort,e.udpPort,e.supervisorHostname,e.supervisorTcpPort,e.startTime,e.stopTime,e.dbName,e.workingDirectory,e.liveDirectory,e.logDir,e.includePath,e.iniPath,e.isService,e.isRunning,e.isPaused,e.isCluster,e.isAgent,e.isSosCommandWebservice,e.param,e.releaseNumber')
            ->orderBy('e.hostname,e.tcpPort')
            ->setMaxResults(1000);

        # Filtrage
        if (isset($Filter['limit']))
            $q->setMaxResults($Filter['limit']);
        if (isset($Filter['hostName']))
            $q->andWhere('e.hostname like :hostName')
                ->setParameter('hostName',$Filter['hostName']);           
        if (isset($Filter['isService']))
            $q->andWhere('e.isService = :isService')
                ->setParameter('isService',($Filter['isService']=='true'?1:0));  
        if (isset($Filter['isRunning']))
            $q->andWhere('e.isRunning = :isRunning')
                ->setParameter('isRunning',($Filter['isRunning']=='true'?1:0));  
        if (isset($Filter['isPaused']))
            $q->andWhere('e.isPaused = :isPaused')
                ->setParameter('isPaused',($Filter['isPaused']=='true'?1:0));  
        if (isset($Filter['isCluster']))
            $q->andWhere('e.isCluster = :isCluster')
                ->setParameter('isCluster',($Filter['isCluster']=='true'?1:0));  
        
        return $q->getQuery()->getResult();
   }
}
