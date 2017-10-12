<?php

namespace Arii\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * AuditRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AuditRepository extends EntityRepository
{
    
   public function findAudit()
   {
        return $this->createQueryBuilder('audit')
              ->Select('audit.logtime,audit.module,u.username,audit.action,audit.status,audit.message,audit.ip')
              ->LeftJoin('AriiUserBundle:User','u',
                    \Doctrine\ORM\Query\Expr\Join::WITH,
                    'audit.username = u.id')
              ->orderBy('audit.logtime','DESC')
              ->getQuery()
              ->getResult();
   }
    
}
