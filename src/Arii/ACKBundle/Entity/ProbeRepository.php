<?php

namespace Arii\ACKBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ProbeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProbeRepository extends EntityRepository
{
    public function findByFilter($name,$description,$type) {
        $q = $this
        ->createQueryBuilder('e')
        ->where('e.name like :name')
        ->andWhere('e.description like :description')
        ->andWhere('BIT_AND(e.obj_type, :type) > 0')
        ->orderBy('e.name')
        ->setParameter('name', $name )
        ->setParameter('description', $description )
        ->setParameter('type', $type )
        ->setMaxResults(1000)
        ->getQuery();
        return $q->getResult();
    }

    public function findHosts($Probe = 'HOST') {        
        $q = $this
        ->createQueryBuilder('e')
        ->select('e.id,e.name,e.title,e.description')
        ->orderBy('e.name,e.obj_type','DESC')
        ->where('e.obj_type = :Probe')
        ->orderBy('e.name')
        ->setParameter('Probe', $Probe )
        ->setMaxResults(1000)        
        ->getQuery();
        return $q->getResult();
    }
}
