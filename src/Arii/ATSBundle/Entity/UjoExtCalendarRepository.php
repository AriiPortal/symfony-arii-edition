<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * UjoCalendarRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UjoExtCalendarRepository extends EntityRepository
{

    public function findCalendars($Filter) {    
        $q = $this
        ->createQueryBuilder('c')
        ->select('c.name as calendarName,c.asDatecon as dateConditions,c.asHolcal as holidayCalendar,c.asAdj as dateAdjustment,c.asHact as holidayAction,c.asNwact as nonHolidayAction,c.asWork as workDays')
        ->setMaxResults(1000);   
        if (isset($Filter['hasEmptyConditions']) and $Filter['hasEmptyConditions']=='true') {
            $q->andWhere("c.asDatecon = '' or c.asDatecon = :null")
              ->setParameter('null', null);
        }
        return $q->getQuery()
                ->getResult();
    }    

}
