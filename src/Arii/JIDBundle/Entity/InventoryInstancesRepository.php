<?php

namespace Arii\JIDBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * InventoryInstancesRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class InventoryInstancesRepository extends EntityRepository
{

   public function findInstances()
   {       
        $q = $this->createQueryBuilder('e')
// Versions superieures
// ,e.version,e.osId ,e.commandUrl,e.url,e.timezone,e.clusterType,e.precedence,e.dbmsName,e.dbmsVersion,e.startedAt,e.supervisorId                
        ->select('e.id,e.schedulerId,e.hostname,e.port,e.liveDirectory,e.created,e.modified');
        return $q->orderBy('e.schedulerId')
                ->setMaxResults(1000)
                ->getQuery()
                ->getResult();       
   }
}
