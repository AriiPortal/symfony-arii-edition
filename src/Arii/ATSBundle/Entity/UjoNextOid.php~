<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UjoNextOid
 *
 * @ORM\Table(name="UJO_NEXT_OID")
 * @ORM\Entity
 */
class UjoNextOid
{
    /**
     * @var string
     *
     * @ORM\Column(name="FIELD", type="string", length=31, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="UJO_NEXT_OID_FIELD_seq", allocationSize=1, initialValue=1)
     */
    private $field;

    /**
     * @var integer
     *
     * @ORM\Column(name="OID", type="integer", nullable=false)
     */
    private $oid;



    /**
     * Get field
     *
     * @return string 
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set oid
     *
     * @param integer $oid
     * @return UjoNextOid
     */
    public function setOid($oid)
    {
        $this->oid = $oid;

        return $this;
    }

    /**
     * Get oid
     *
     * @return integer 
     */
    public function getOid()
    {
        return $this->oid;
    }
}
