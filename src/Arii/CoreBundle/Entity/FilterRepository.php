<?php

namespace Arii\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * FilterRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FilterRepository extends EntityRepository
{

    public function findInternals() {
        
        $q = $this
        ->createQueryBuilder('f')
        ->where('f.filter_type = :type')
        ->OrderBy('f.name')
        ->setParameter('type', 2)
        ->getQuery();

        return $q->getResult();
    }

}
