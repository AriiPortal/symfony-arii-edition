<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UjoJobRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UjoJobRepository extends EntityRepository
{

    public function findIdByName($jobName) { 
        return $this->createQueryBuilder('e')
        ->select('e.joid')
        ->where('e.jobName = :jobName')
        ->andWhere('e.isCurrver = 1')
        ->setParameter('jobName',$jobName)
        ->getQuery()        
        ->getSingleResult();
    }

    public function findChildren($boxJoid) {
        $q = $this
        ->createQueryBuilder('e')
        ->distinct()
        ->where('e.boxJoid like :boxJoid')
        ->andWhere('e.isCurrver = 1')                
        ->setParameter('boxJoid',$boxJoid); 

        return $q->orderBy('e.hasCondition,e.jobName')
        ->getQuery()
        ->setMaxResults(100)
        ->getResult();
    }
    
}
