<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UjoIntcodes
 *
 * @ORM\Table(name="UJO_INTCODES")
 * @ORM\Entity
 */
class UjoIntcodes
{
    /**
     * @var integer
     *
     * @ORM\Column(name="CODE", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="FLD", type="string", length=30, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $fld;

    /**
     * @var string
     *
     * @ORM\Column(name="TEXT", type="string", length=30, nullable=true)
     */
    private $text;



    /**
     * Set code
     *
     * @param integer $code
     * @return UjoIntcodes
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return integer 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set fld
     *
     * @param string $fld
     * @return UjoIntcodes
     */
    public function setFld($fld)
    {
        $this->fld = $fld;

        return $this;
    }

    /**
     * Get fld
     *
     * @return string 
     */
    public function getFld()
    {
        return $this->fld;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return UjoIntcodes
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }
}
