<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UjoAuditInfo
 *
 * @ORM\Table(name="UJO_AUDIT_INFO")
 * @ORM\Entity(readOnly=true,repositoryClass="Arii\ATSBundle\Entity\UjoAuditInfoRepository")
 */
class UjoAuditInfo
{
    /**
     * @var integer
     *
     * @ORM\Column(name="AUDIT_INFO_NUM", type="integer" )
     * @ORM\Id
     */
    private $auditInfoNum;

    /**
     * @var string
     *
     * @ORM\Column(name="ENTITY", type="string", length=12)
     */
    private $entity;

    /**
     * @var integer
     *
     * @ORM\Column(name="TIME", type="integer" )
     */
    private $time;

    /**
     * @var string
     *
     * @ORM\Column(name="TYPE", type="string", length=1)
     */
    private $type;


    /**
     * Set auditInfoNum
     *
     * @param integer $auditInfoNum
     * @return UjoAuditInfo
     */
    public function setAuditInfoNum($auditInfoNum)
    {
        $this->auditInfoNum = $auditInfoNum;

        return $this;
    }

    /**
     * Get auditInfoNum
     *
     * @return integer 
     */
    public function getAuditInfoNum()
    {
        return $this->auditInfoNum;
    }

    /**
     * Set entity
     *
     * @param string $entity
     * @return UjoAuditInfo
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return string 
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set time
     *
     * @param integer $time
     * @return UjoAuditInfo
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return integer 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return UjoAuditInfo
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }
}
