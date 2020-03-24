<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UjoExtCalendar
 *
 * @ORM\Table(name="UJO_EXT_CALENDAR")
 * @ORM\Entity(readOnly=true,repositoryClass="Arii\ATSBundle\Entity\UjoExtCalendarRepository")
 */
class UjoExtCalendar
{
    /**
     * @var integer
     *
     * @ORM\Column(name="AS_ADJ", type="integer", nullable=true)
     */
    private $asAdj;

    /**
     * @var string
     *
     * @ORM\Column(name="AS_CYCCAL", type="string", length=30, nullable=true)
     */
    private $asCyccal;

    /**
     * @var string
     *
     * @ORM\Column(name="AS_DATECON", type="string", length=255, nullable=true)
     */
    private $asDatecon;

    /**
     * @var string
     *
     * @ORM\Column(name="AS_HACT", type="string", length=1, nullable=true)
     */
    private $asHact;

    /**
     * @var string
     *
     * @ORM\Column(name="AS_HOLCAL", type="string", length=30, nullable=true)
     */
    private $asHolcal;

    /**
     * @var string
     *
     * @ORM\Column(name="AS_NWACT", type="string", length=1, nullable=true)
     */
    private $asNwact;

    /**
     * @var string
     *
     * @ORM\Column(name="AS_WORK", type="string", length=7, nullable=true)
     */
    private $asWork;

    /**
     * @var integer
     *
     * @ORM\Column(name="CAL_ID", type="integer", nullable=false)
     */
    private $calId;

    /**
     * @var string
     *
     * @ORM\Column(name="NAME", type="string", length=64, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="UJO_EXT_CALENDAR_NAME_seq", allocationSize=1, initialValue=1)
     */
    private $name;



    /**
     * Set asAdj
     *
     * @param integer $asAdj
     * @return UjoExtCalendar
     */
    public function setAsAdj($asAdj)
    {
        $this->asAdj = $asAdj;

        return $this;
    }

    /**
     * Get asAdj
     *
     * @return integer 
     */
    public function getAsAdj()
    {
        return $this->asAdj;
    }

    /**
     * Set asCyccal
     *
     * @param string $asCyccal
     * @return UjoExtCalendar
     */
    public function setAsCyccal($asCyccal)
    {
        $this->asCyccal = $asCyccal;

        return $this;
    }

    /**
     * Get asCyccal
     *
     * @return string 
     */
    public function getAsCyccal()
    {
        return $this->asCyccal;
    }

    /**
     * Set asDatecon
     *
     * @param string $asDatecon
     * @return UjoExtCalendar
     */
    public function setAsDatecon($asDatecon)
    {
        $this->asDatecon = $asDatecon;

        return $this;
    }

    /**
     * Get asDatecon
     *
     * @return string 
     */
    public function getAsDatecon()
    {
        return $this->asDatecon;
    }

    /**
     * Set asHact
     *
     * @param string $asHact
     * @return UjoExtCalendar
     */
    public function setAsHact($asHact)
    {
        $this->asHact = $asHact;

        return $this;
    }

    /**
     * Get asHact
     *
     * @return string 
     */
    public function getAsHact()
    {
        return $this->asHact;
    }

    /**
     * Set asHolcal
     *
     * @param string $asHolcal
     * @return UjoExtCalendar
     */
    public function setAsHolcal($asHolcal)
    {
        $this->asHolcal = $asHolcal;

        return $this;
    }

    /**
     * Get asHolcal
     *
     * @return string 
     */
    public function getAsHolcal()
    {
        return $this->asHolcal;
    }

    /**
     * Set asNwact
     *
     * @param string $asNwact
     * @return UjoExtCalendar
     */
    public function setAsNwact($asNwact)
    {
        $this->asNwact = $asNwact;

        return $this;
    }

    /**
     * Get asNwact
     *
     * @return string 
     */
    public function getAsNwact()
    {
        return $this->asNwact;
    }

    /**
     * Set asWork
     *
     * @param string $asWork
     * @return UjoExtCalendar
     */
    public function setAsWork($asWork)
    {
        $this->asWork = $asWork;

        return $this;
    }

    /**
     * Get asWork
     *
     * @return string 
     */
    public function getAsWork()
    {
        return $this->asWork;
    }

    /**
     * Set calId
     *
     * @param integer $calId
     * @return UjoExtCalendar
     */
    public function setCalId($calId)
    {
        $this->calId = $calId;

        return $this;
    }

    /**
     * Get calId
     *
     * @return integer 
     */
    public function getCalId()
    {
        return $this->calId;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
}
