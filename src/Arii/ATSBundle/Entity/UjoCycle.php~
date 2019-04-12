<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UjoCycle
 *
 * @ORM\Table(name="UJO_CYCLE")
 * @ORM\Entity
 */
class UjoCycle
{
    /**
     * @var string
     *
     * @ORM\Column(name="CYCNAME", type="string", length=64, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $cycname;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="CYCPEREN", type="datetime", nullable=false)
     */
    private $cycperen;

    /**
     * @var integer
     *
     * @ORM\Column(name="CYCPERIOD", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $cycperiod;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="CYCPERST", type="datetime", nullable=false)
     */
    private $cycperst;



    /**
     * Set cycname
     *
     * @param string $cycname
     * @return UjoCycle
     */
    public function setCycname($cycname)
    {
        $this->cycname = $cycname;

        return $this;
    }

    /**
     * Get cycname
     *
     * @return string 
     */
    public function getCycname()
    {
        return $this->cycname;
    }

    /**
     * Set cycperen
     *
     * @param \DateTime $cycperen
     * @return UjoCycle
     */
    public function setCycperen($cycperen)
    {
        $this->cycperen = $cycperen;

        return $this;
    }

    /**
     * Get cycperen
     *
     * @return \DateTime 
     */
    public function getCycperen()
    {
        return $this->cycperen;
    }

    /**
     * Set cycperiod
     *
     * @param integer $cycperiod
     * @return UjoCycle
     */
    public function setCycperiod($cycperiod)
    {
        $this->cycperiod = $cycperiod;

        return $this;
    }

    /**
     * Get cycperiod
     *
     * @return integer 
     */
    public function getCycperiod()
    {
        return $this->cycperiod;
    }

    /**
     * Set cycperst
     *
     * @param \DateTime $cycperst
     * @return UjoCycle
     */
    public function setCycperst($cycperst)
    {
        $this->cycperst = $cycperst;

        return $this;
    }

    /**
     * Get cycperst
     *
     * @return \DateTime 
     */
    public function getCycperst()
    {
        return $this->cycperst;
    }
}
