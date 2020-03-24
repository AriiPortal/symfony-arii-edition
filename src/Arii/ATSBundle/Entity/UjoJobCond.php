<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UjoJobCond
 *
 * @ORM\Table(name="UJO_JOB_COND", indexes={@ORM\Index(name="job_cond_PUC", columns={"COND_MODE", "JOID", "INDX", "OVER_NUM"})})
 * @ORM\Entity(readOnly=true,repositoryClass="Arii\ATSBundle\Entity\UjoJobCondRepository")
 */
class UjoJobCond
{
    
    /**
     * @var integer
     *
     * @ORM\Column(name="COND_MODE", type="integer", nullable=false)
     */
    private $condMode;

    /**
     * @var integer
     * @ORM\Id 
     * @ORM\GeneratedValue(strategy="NONE")
     * 
     * @ORM\Column(name="JOID", type="integer", nullable=false)
     */
    private $joid;

    /**
     * @var integer
     *
     * @ORM\Column(name="INDX", type="integer", nullable=false)
     */
    private $indx;

    /**
     * @var string
     *
     * @ORM\Column(name="TYPE", type="string", length=1, nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="COND_JOB_NAME", type="string", length=64, nullable=true)
     */
    private $condJobName;

    /**
     * @var string
     *
     * @ORM\Column(name="COND_JOB_AUTOSERV", type="string", length=30, nullable=true)
     */
    private $condJobAutoserv;

    /**
     * @var string
     *
     * @ORM\Column(name="OPERATOR", type="string", length=2, nullable=true)
     */
    private $operator;

    /**
     * @var integer
     *
     * @ORM\Column(name="VALUE", type="integer", nullable=false)
     */
    private $value;

    /**
     * @var integer
     *
     * @ORM\Column(name="INDX_PTR", type="integer", nullable=false)
     */
    private $indexPtr;

    /**
     * @var string
     *
     * @ORM\Column(name="TEST_GLOVALUE", type="string", length=100, nullable=true)
     */
    private $testGlovalue;

    /**
     * @var integer
     *
     * @ORM\Column(name="LOOKBACK_SECS", type="integer", nullable=false)
     */
    private $lookbackSecs;

    /**
     * @var integer
     *
     * @ORM\Column(name="OVER_NUM", type="integer", nullable=false)
     */
    private $overNum;
    

    /**
     * Set cond_mode
     *
     * @param integer $condMode
     * @return UjoJobCond
     */
    public function setCondMode($condMode)
    {
        $this->condMode = $condMode;

        return $this;
    }

    /**
     * Get cond_mode
     *
     * @return integer 
     */
    public function getCondMode()
    {
        return $this->condMode;
    }

    /**
     * Set joid
     *
     * @param integer $joid
     * @return UjoJobCond
     */
    public function setJoid($joid)
    {
        $this->joid = $joid;

        return $this;
    }

    /**
     * Get joid
     *
     * @return integer 
     */
    public function getJoid()
    {
        return $this->joid;
    }

    /**
     * Set indx
     *
     * @param integer $indx
     * @return UjoJobCond
     */
    public function setIndx($indx)
    {
        $this->indx = $indx;

        return $this;
    }

    /**
     * Get indx
     *
     * @return integer 
     */
    public function getIndx()
    {
        return $this->indx;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return UjoJobCond
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

    /**
     * Set condJobName
     *
     * @param string $condJobName
     * @return UjoJobCond
     */
    public function setCondJobName($condJobName)
    {
        $this->condJobName = $condJobName;

        return $this;
    }

    /**
     * Get condJobName
     *
     * @return string 
     */
    public function getCondJobName()
    {
        return $this->condJobName;
    }

    /**
     * Set condJobAutoserv
     *
     * @param string $condJobAutoserv
     * @return UjoJobCond
     */
    public function setCondJobAutoserv($condJobAutoserv)
    {
        $this->condJobAutoserv = $condJobAutoserv;

        return $this;
    }

    /**
     * Get condJobAutoserv
     *
     * @return string 
     */
    public function getCondJobAutoserv()
    {
        return $this->condJobAutoserv;
    }

    /**
     * Set operator
     *
     * @param string $operator
     * @return UjoJobCond
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * Get operator
     *
     * @return string 
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * Set value
     *
     * @param integer $value
     * @return UjoJobCond
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return integer 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set indexPtr
     *
     * @param integer $indexPtr
     * @return UjoJobCond
     */
    public function setIndexPtr($indexPtr)
    {
        $this->indexPtr = $indexPtr;

        return $this;
    }

    /**
     * Get indexPtr
     *
     * @return integer 
     */
    public function getIndexPtr()
    {
        return $this->indexPtr;
    }

    /**
     * Set testGlovalue
     *
     * @param string $testGlovalue
     * @return UjoJobCond
     */
    public function setTestGlovalue($testGlovalue)
    {
        $this->testGlovalue = $testGlovalue;

        return $this;
    }

    /**
     * Get testGlovalue
     *
     * @return string 
     */
    public function getTestGlovalue()
    {
        return $this->testGlovalue;
    }

    /**
     * Set lookbackSecs
     *
     * @param integer $lookbackSecs
     * @return UjoJobCond
     */
    public function setLookbackSecs($lookbackSecs)
    {
        $this->lookbackSecs = $lookbackSecs;

        return $this;
    }

    /**
     * Get lookbackSecs
     *
     * @return integer 
     */
    public function getLookbackSecs()
    {
        return $this->lookbackSecs;
    }

    /**
     * Set overNum
     *
     * @param integer $overNum
     * @return UjoJobCond
     */
    public function setOverNum($overNum)
    {
        $this->overNum = $overNum;

        return $this;
    }

    /**
     * Get overNum
     *
     * @return integer 
     */
    public function getOverNum()
    {
        return $this->overNum;
    }
}
