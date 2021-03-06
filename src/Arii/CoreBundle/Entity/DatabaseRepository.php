<?php

namespace Arii\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * DatabaseRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DatabaseRepository extends EntityRepository
{
   public function findDatabases()
   {
        return $this->createQueryBuilder('con')
            ->Select('db.id','db.name','db.type','db.role','db.description','db.dbname','db.driver',
                    'con.login','con.interface','con.port','con.password')
            ->leftJoin('Arii\CoreBundle\Entity\Database','db',\Doctrine\ORM\Query\Expr\Join::WITH,'db.connection = con.id')                
            ->orderBy('db.name','ASC')
            ->getQuery()
            ->getResult();
    }
    
}
