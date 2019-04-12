<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UjoRealResource
 *
 * @ORM\Table(name="UJO_REAL_RESOURCE", indexes={@ORM\Index(name="xak1ujo_real_resource", columns={"NAME"})})
 * @ORM\Entity
 */
class UjoRealResource
{
    /**
     * @var string
     *
     * @ORM\Column(name="KEYWORD", type="string", length=256, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $keyword;

    /**
     * @var string
     *
     * @ORM\Column(name="MAX_VALUE", type="string", length=20, nullable=true)
     */
    private $maxValue;

    /**
     * @var string
     *
     * @ORM\Column(name="MIN_VALUE", type="string", length=20, nullable=true)
     */
    private $minValue;

    /**
     * @var string
     *
     * @ORM\Column(name="NAME", type="string", length=256, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="OPTIONAL", type="smallint", nullable=true)
     */
    private $optional;

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
     * @ORM\Column(name="TYPE", type="smallint", nullable=false)
     */
    private $type;



    /**
     * Set keyword
     *
     * @param string $keyword
     * @return UjoRealResource
     */
    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;

        return $this;
    }

    /**
     * Get keyword
     *
     * @return string 
     */
    public function getKeyword()
    {
        return $this->keyword;
    }

    /**
     * Set maxValue
     *
     * @param string $maxValue
     * @return UjoRealResource
     */
    public function setMaxValue($maxValue)
    {
        $this->maxValue = $maxValue;

        return $this;
    }

    /**
     * Get maxValue
     *
     * @return string 
     */
    public function getMaxValue()
    {
        return $this->maxValue;
    }

    /**
     * Set minValue
     *
     * @param string $minValue
     * @return UjoRealResource
     */
    public function setMinValue($minValue)
    {
        $this->minValue = $minValue;

        return $this;
    }

    /**
     * Get minValue
     *
     * @return string 
     */
    public function getMinValue()
    {
        return $this->minValue;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return UjoRealResource
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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

    /**
     * Set optional
     *
     * @param integer $optional
     * @return UjoRealResource
     */
    public function setOptional($optional)
    {
        $this->optional = $optional;

        return $this;
    }

    /**
     * Get optional
     *
     * @return integer 
     */
    public function getOptional()
    {
        return $this->optional;
    }

    /**
     * Set roid
     *
     * @param integer $roid
     * @return UjoRealResource
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
     * Set type
     *
     * @param integer $type
     * @return UjoRealResource
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }
}
