<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UjoReswaitQue
 *
 * @ORM\Table(name="UJO_RESWAIT_QUE", indexes={@ORM\Index(name="xak1ujo_reswait_que", columns={"QUE_NAME"}), @ORM\Index(name="xak2ujo_reswait_que", columns={"RES_MACH"})})
 * @ORM\Entity
 */
class UjoReswaitQue
{
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
     * @ORM\Column(name="PRIORITY", type="integer", nullable=true)
     */
    private $priority;

    /**
     * @var string
     *
     * @ORM\Column(name="QUE_NAME", type="string", length=161, nullable=false)
     */
    private $queName;

    /**
     * @var string
     *
     * @ORM\Column(name="RES_MACH", type="string", length=80, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $resMach;

    /**
     * @var integer
     *
     * @ORM\Column(name="STAMP", type="integer", nullable=true)
     */
    private $stamp;

    /**
     * @var integer
     *
     * @ORM\Column(name="WF_JOID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $wfJoid;



    /**
     * Set joid
     *
     * @param integer $joid
     * @return UjoReswaitQue
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
     * Set priority
     *
     * @param integer $priority
     * @return UjoReswaitQue
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer 
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set queName
     *
     * @param string $queName
     * @return UjoReswaitQue
     */
    public function setQueName($queName)
    {
        $this->queName = $queName;

        return $this;
    }

    /**
     * Get queName
     *
     * @return string 
     */
    public function getQueName()
    {
        return $this->queName;
    }

    /**
     * Set resMach
     *
     * @param string $resMach
     * @return UjoReswaitQue
     */
    public function setResMach($resMach)
    {
        $this->resMach = $resMach;

        return $this;
    }

    /**
     * Get resMach
     *
     * @return string 
     */
    public function getResMach()
    {
        return $this->resMach;
    }

    /**
     * Set stamp
     *
     * @param integer $stamp
     * @return UjoReswaitQue
     */
    public function setStamp($stamp)
    {
        $this->stamp = $stamp;

        return $this;
    }

    /**
     * Get stamp
     *
     * @return integer 
     */
    public function getStamp()
    {
        return $this->stamp;
    }

    /**
     * Set wfJoid
     *
     * @param integer $wfJoid
     * @return UjoReswaitQue
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
