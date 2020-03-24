<?php

namespace Arii\MFTBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * TransfersRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TransfersRepository extends EntityRepository
{
    public function findTransfersByPartner($partnerId,$Filter=[]) { 
        $q = $this->createQueryBuilder('e')
        ->select('e.id,e.name,e.title,e.env,e.description,e.steps,e.step_start,e.step_end,e.change_time,e.change_user,'
                . '(e.schedule) as schedule_id,(e.summary) as summary_id')
        ->where('e.partner = :partnerId')
        ->orderBy('e.title')
        ->setParameter('partnerId',$partnerId);
        if (isset($Filter['env']))
            $q->andWhere('e.env = :env')
                ->setParameter('env',$Filter['env']);        
        return $q->getQuery()->getResult();
    }
    
}