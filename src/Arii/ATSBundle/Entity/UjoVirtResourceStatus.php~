<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UjoVirtResourceStatus
 *
 * @ORM\Table(name="UJO_VIRT_RESOURCE_STATUS", indexes={@ORM\Index(name="xak1ujo_virt_resource_status", columns={"JOID", "JOB_VER", "WF_JOID"})})
 * @ORM\Entity
 */
class UjoVirtResourceStatus
{
    /**
     * @var integer
     *
     * @ORM\Column(name="AMOUNT_IN_USE", type="integer", nullable=false)
     */
    private $amountInUse;

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
     * @var string
     *
     * @ORM\Column(name="MACH_NAME", type="string", length=80, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $machName;

    /**
     * @var integer
     *
     * @ORM\Column(name="NTRY", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $ntry;

    /**
     * @var integer
     *
     * @ORM\Column(name="ROID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $roid;

    /**
     * @var integer
     *
     * @ORM\Column(name="RUN_NUM", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $runNum;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="TIME", type="datetime", nullable=true)
     */
    private $time;

    /**
     * @var integer
     *
     * @ORM\Column(name="WF_JOID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $wfJoid;



    /**
     * Set amountInUse
     *
     * @param integer $amountInUse
     * @return UjoVirtResourceStatus
     */
    public function setAmountInUse($amountInUse)
    {
        $this->amountInUse = $amountInUse;

        return $this;
    }

    /**
     * Get amountInUse
     *
     * @return integer 
     */
    public function getAmountInUse()
    {
        return $this->amountInUse;
    }

    /**
     * Set jobVer
     *
     * @param integer $jobVer
     * @return UjoVirtResourceStatus
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
     * @return UjoVirtResourceStatus
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
     * Set machName
     *
     * @param string $machName
     * @return UjoVirtResourceStatus
     */
    public function setMachName($machName)
    {
        $this->machName = $machName;

        return $this;
    }

    /**
     * Get machName
     *
     * @return string 
     */
    public function getMachName()
    {
        return $this->machName;
    }

    /**
     * Set ntry
     *
     * @param integer $ntry
     * @return UjoVirtResourceStatus
     */
    public function setNtry($ntry)
    {
        $this->ntry = $ntry;

        return $this;
    }

    /**
     * Get ntry
     *
     * @return integer 
     */
    public function getNtry()
    {
        return $this->ntry;
    }

    /**
     * Set roid
     *
     * @param integer $roid
     * @return UjoVirtResourceStatus
     */
    public function setRoid($roid)
    {
        $this->roid = $roid;

        return $this;
    }

    /**
     * Get roid
     *
     * @return integer 
     */
    public function getRoid()
    {
        return $this->roid;
    }

    /**
     * Set runNum
     *
     * @param integer $runNum
     * @return UjoVirtResourceStatus
     */
    public function setRunNum($runNum)
    {
        $this->runNum = $runNum;

        return $this;
    }

    /**
     * Get runNum
     *
     * @return integer 
     */
    public function getRunNum()
    {
        return $this->runNum;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     * @return UjoVirtResourceStatus
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set wfJoid
     *
     * @param integer $wfJoid
     * @return UjoVirtResourceStatus
     */
    public function setWfJoid($wfJoid)
    {
        $this->wfJoid = $wfJoid;

        return $this;
    }

    /**
     * Get wfJoid
     *
     * @return integer 
     */
    public function getWfJoid()
    {
        return $this->wfJoid;
    }
}
