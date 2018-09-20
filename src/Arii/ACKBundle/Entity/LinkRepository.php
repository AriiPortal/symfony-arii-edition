<?php

namespace Arii\ACKBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * LinkRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LinkRepository extends EntityRepository
{
    public function findLinks($id,$type='DEPENDS') {
        return $this->createQueryBuilder('e')
            ->select('e')
            ->leftjoin('AriiACKBundle:Object','o',\Doctrine\ORM\Query\Expr\Join::WITH,'e.obj_to = o.id')
            ->where('e.obj_from = :id')
            ->andWhere('e.link_type = :type')
            ->setParameter('id', $id)
            ->setParameter('type',$type)
            ->getQuery();
    }
    
}
