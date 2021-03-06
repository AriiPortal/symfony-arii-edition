<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UjoAuditInfoRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UjoAuditInfoRepository extends EntityRepository
{
    public function findInfo() {        
        $q = $this
        ->createQueryBuilder('i')
        ->select('i.entity','i.time','i.type')
/*        ->where('i.type = :type')
        ->setParameter('type','c')
*/        ->orderBy('i.time','desc')
        ->setMaxResults(1000)
        ->getQuery();
        return $q->getResult();
    }
    
    public function findTypes() {        
        $q = $this
        ->createQueryBuilder('i')
        ->select('i.type')
        ->groupBy('i.type')
        ->setMaxResults(1000)
        ->getQuery();
        return $q->getResult();
    }
    
    public function findAudit($Filter) {        
        $q = $this
        ->createQueryBuilder('i')
        ->select('m.auditInfoNum as auditId','i.entity','i.time as eventTime','i.type as eventType', 'm.seqNo','m.attribute','m.value1 as value','m.value2')
        ->leftjoin('AriiATSBundle:UjoAuditMsg','m',\Doctrine\ORM\Query\Expr\Join::WITH,'i.auditInfoNum = m.auditInfoNum');
        if (isset($Filter['jobName']) and ($Filter['jobName']!='')) {
            $q->andWhere('m.value1 like :jobName')
            ->andWhere("m.attribute like 'job_name'")
            ->setParameter('jobName',$Filter['jobName']);
        }
        if (isset($Filter['eventType']) and ($Filter['eventType']!='')) {
            $q->andWhere('i.type like :type')
            ->setParameter('type',strtoupper(substr($Filter['eventType'],0,1)));
        }
        if (isset($Filter['minDate'])) {
            $q->andWhere('i.time > :minDate')
            ->setParameter('minDate', $Filter['minDate']->format("U")); 
        }
        if (isset($Filter['maxDate'])) {
            $q->andWhere('i.time < :maxDate')
            ->setParameter('maxDate', $Filter['maxDate']->format("U")); 
        }   
        
        return $q->orderBy('i.time','desc')
        ->setMaxResults(1000)
        ->getQuery()
        ->getResult();
    }
    
    public function findSendevent() {        
        $q = $this
        ->createQueryBuilder('i')
        ->select('m.auditInfoNum', 'm.seqNo','m.attribute','m.value1','m.value2','m.isEdit','i.entity','i.time','i.type')
        ->leftjoin('AriiATSBundle:UjoAuditMsg','m',\Doctrine\ORM\Query\Expr\Join::WITH,'i.auditInfoNum = m.auditInfoNum')
        ->where('i.type = :type')
        ->andWhere('m.attribute = :attr')
        ->andWhere('m.seqNo = :seq')
        ->orderBy('i.time','desc')
        ->setParameter('type','S')
        ->setParameter('attr','command')
        ->setParameter('seq','3')
        ->setMaxResults(1000)
        ->getQuery();
        return $q->getResult();
    }

    public function findJobs() {        
        $q = $this
        ->createQueryBuilder('i')
        ->select('m.auditInfoNum', 'm.seqNo','m.attribute','m.value1','m.value2','m.isEdit','i.entity','i.time','i.type')
        ->leftjoin('AriiATSBundle:UjoAuditMsg','m',\Doctrine\ORM\Query\Expr\Join::WITH,'i.auditInfoNum = m.auditInfoNum')
        ->where('i.type = :type')
        ->andWhere('m.seqNo = :seq')
        ->orderBy('i.time','desc')
        ->setParameter('type','J')
        ->setParameter('seq','1')
        ->setMaxResults(1000)
        ->getQuery();
        return $q->getResult();
    }

}
