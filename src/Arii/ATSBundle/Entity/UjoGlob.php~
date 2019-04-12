<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UjoGlob
 *
 * @ORM\Table(name="UJO_GLOB")
 * @ORM\Entity
 */
class UjoGlob
{
    /**
     * @var string
     *
     * @ORM\Column(name="GLO_NAME", type="string", length=64, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="UJO_GLOB_GLO_NAME_seq", allocationSize=1, initialValue=1)
     */
    private $gloName;

    /**
     * @var string
     *
     * @ORM\Column(name="OWNER", type="string", length=145, nullable=true)
     */
    private $owner;

    /**
     * @var string
     *
     * @ORM\Column(name="PERMISSION", type="string", length=30, nullable=true)
     */
    private $permission;

    /**
     * @var string
     *
     * @ORM\Column(name="VALUE", type="string", length=100, nullable=true)
     */
    private $value;

    /**
     * @var integer
     *
     * @ORM\Column(name="VALUE_SET_TIME", type="integer", nullable=true)
     */
    private $valueSetTime;



    /**
     * Get gloName
     *
     * @return string 
     */
    public function getGloName()
    {
        return $this->gloName;
    }

    /**
     * Set owner
     *
     * @param string $owner
     * @return UjoGlob
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return string 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set permission
     *
     * @param string $permission
     * @return UjoGlob
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * Get permission
     *
     * @return string 
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return UjoGlob
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set valueSetTime
     *
     * @param integer $valueSetTime
     * @return UjoGlob
     */
    public function setValueSetTime($valueSetTime)
    {
        $this->valueSetTime = $valueSetTime;

        return $this;
    }

    /**
     * Get valueSetTime
     *
     * @return integer 
     */
    public function getValueSetTime()
    {
        return $this->valueSetTime;
    }
}
