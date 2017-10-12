<?php

namespace Arii\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * MessageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MessageRepository extends EntityRepository
{

   public function findInbox($to)
   {
        return $this->createQueryBuilder('msg')
              ->Select('msg.title,msg.msg_text,msg.msg_sent,(msg.msg_from) as msg_from')
              ->Where('msg.msg_to = :user')
              ->setParameter('user',$to)
              ->getQuery()
              ->getResult();
   }

   public function findOutbox($from)
   {
        return $this->createQueryBuilder('msg')
              ->Select('msg.title,msg.msg_text')
              ->Where('msg.msg_from = :user')
              ->setParameter('user',$from)
              ->getQuery()
              ->getResult();
   }
   
}