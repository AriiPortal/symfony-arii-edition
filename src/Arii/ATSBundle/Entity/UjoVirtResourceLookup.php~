<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UjoVirtResourceLookup
 *
 * @ORM\Table(name="UJO_VIRT_RESOURCE_LOOKUP")
 * @ORM\Entity
 */
class UjoVirtResourceLookup
{
    /**
     * @var integer
     *
     * @ORM\Column(name="GLOBAL_IND", type="smallint", nullable=false)
     */
    private $globalInd;

    /**
     * @var string
     *
     * @ORM\Column(name="RES_NAME", type="string", length=128, nullable=true)
     */
    private $resName;

    /**
     * @var integer
     *
     * @ORM\Column(name="ROID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="UJO_VIRT_RESOURCE_LOOKUP_ROID_", allocationSize=1, initialValue=1)
     */
    private $roid;



    /**
     * Set globalInd
     *
     * @param integer $globalInd
     * @return UjoVirtResourceLookup
     */
    public function setGlobalInd($globalInd)
    {
        $this->globalInd = $globalInd;

        return $this;
    }

    /**
     * Get globalInd
     *
     * @return integer 
     */
    public function getGlobalInd()
    {
        return $this->globalInd;
    }

    /**
     * Set resName
     *
     * @param string $resName
     * @return UjoVirtResourceLookup
     */
    public function setResName($resName)
    {
        $this->resName = $resName;

        return $this;
    }

    /**
     * Get resName
     *
     * @return string 
     */
    public function getResName()
    {
        return $this->resName;
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
}
