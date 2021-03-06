<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UjoCredRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UjoCredRepository extends EntityRepository
{

    public function findUsers($Filter) {    
        $q = $this
        ->createQueryBuilder('u')
        ->select('u.principal as userName,u.domainName,u.credDomain')
        ->orderBy('u.owner')
        ->setMaxResults(100);    
        if (isset($Filter['userName'])) {
            $q->andWhere('u.principal like :userName')
            ->setParameter('userName', strtoupper($Filter['userName'])); 
        }
        if (isset($Filter['domainName'])) {
            $q->andWhere('u.domainName like :domainName')
            ->setParameter('domainName',strtoupper($Filter['domainName'])); 
        }
        return $q->getQuery()
                ->getResult();
    }    

}
