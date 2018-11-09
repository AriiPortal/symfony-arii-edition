<?php

namespace Arii\ACKBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * GraphProbeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GraphProbeRepository extends EntityRepository
{
    // Les sondes d'un graph
    public function findProbes($graph) {        
        $q = $this
        ->createQueryBuilder('e')
        ->select('e')
        ->where('e.graph = :graph')
        ->setParameter('graph', $graph )
        ->getQuery();
        return $q->getResult();
    }

    // La sonde d'un graph
    public function findProbe($graph,$probe) {        
        $q = $this
        ->createQueryBuilder('e')
        ->select('e')
        ->where('e.graph = :graph')
        ->andWhere('e.probe = :probe')
        ->setParameter('graph', $graph )
        ->setParameter('probe', $probe )
        ->getQuery();
        return $q->getResult();
    }
    
}
