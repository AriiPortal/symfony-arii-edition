<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UjoAuditInfo
 *
 * @ORM\Table(name="UJO_AUDIT_MSG")
 * @ORM\Entity(readOnly=true,repositoryClass="Arii\ATSBundle\Entity\UjoAuditMsgRepository")
 */
class UjoAuditMsg
{
    /**
     * @var string
     *
     * @ORM\Column(name="ATTRIBUTE", type="string", length=30, nullable=true)
     */
    private $attribute;

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
     * @ORM\Column(name="IS_EDIT", type="string", length=1)
     */
    private $isEdit;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="SEQ_NO", type="integer" )
     */
    private $seqNo;

    /**
     * @var string
     *
     * @ORM\Column(name="VALUE1", type="string", length=2000,nullable=true)
     */
    private $value1;
    
    /**
     * @var string
     *
     * @ORM\Column(name="VALUE2", type="string", length=2000,nullable=true)
     */
    private $value2;
    

    /**
     * Set attribute
     *
     * @param string $attribute
     * @return UjoAuditMsg
     */
    public function setAttribute($attribute)
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * Get attribute
     *
     * @return string 
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Set auditInfoNum
     *
     * @param integer $auditInfoNum
     * @return UjoAuditMsg
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
     * Set isEdit
     *
     * @param string $isEdit
     * @return UjoAuditMsg
     */
    public function setIsEdit($isEdit)
    {
        $this->isEdit = $isEdit;

        return $this;
    }

    /**
     * Get isEdit
     *
     * @return string 
     */
    public function getIsEdit()
    {
        return $this->isEdit;
    }

    /**
     * Set seqNo
     *
     * @param integer $seqNo
     * @return UjoAuditMsg
     */
    public function setSeqNo($seqNo)
    {
        $this->seqNo = $seqNo;

        return $this;
    }

    /**
     * Get seqNo
     *
     * @return integer 
     */
    public function getSeqNo()
    {
        return $this->seqNo;
    }

    /**
     * Set value1
     *
     * @param string $value1
     * @return UjoAuditMsg
     */
    public function setValue1($value1)
    {
        $this->value1 = $value1;

        return $this;
    }

    /**
     * Get value1
     *
     * @return string 
     */
    public function getValue1()
    {
        return $this->value1;
    }

    /**
     * Set value2
     *
     * @param string $value2
     * @return UjoAuditMsg
     */
    public function setValue2($value2)
    {
        $this->value2 = $value2;

        return $this;
    }

    /**
     * Get value2
     *
     * @return string 
     */
    public function getValue2()
    {
        return $this->value2;
    }
}
