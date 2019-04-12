<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UjoJobblob
 *
 * @ORM\Table(name="UJO_JOBBLOB")
 * @ORM\Entity
 */
class UjoJobblob
{
    /**
     * @var string
     *
     * @ORM\Column(name="DATA", type="blob", nullable=true)
     */
    private $data;

    /**
     * @var integer
     *
     * @ORM\Column(name="JOB_VER", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $jobVer;

    /**
     * @var integer
     *
     * @ORM\Column(name="JOID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $joid;

    /**
     * @var integer
     *
     * @ORM\Column(name="OVER_NUM", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $overNum;

    /**
     * @var integer
     *
     * @ORM\Column(name="SEQUENCE", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $sequence;

    /**
     * @var string
     *
     * @ORM\Column(name="TYPE", type="string", length=1, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $type;



    /**
     * Set data
     *
     * @param string $data
     * @return UjoJobblob
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set jobVer
     *
     * @param integer $jobVer
     * @return UjoJobblob
     */
    public function setJobVer($jobVer)
    {
        $this->jobVer = $jobVer;

        return $this;
    }

    /**
     * Get jobVer
     *
     * @return integer 
     */
    public function getJobVer()
    {
        return $this->jobVer;
    }

    /**
     * Set joid
     *
     * @param integer $joid
     * @return UjoJobblob
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
     * Set overNum
     *
     * @param integer $overNum
     * @return UjoJobblob
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

    /**
     * Set sequence
     *
     * @param integer $sequence
     * @return UjoJobblob
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;

        return $this;
    }

    /**
     * Get sequence
     *
     * @return integer 
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return UjoJobblob
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
