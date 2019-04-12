<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UjoPatch
 *
 * @ORM\Table(name="UJO_PATCH")
 * @ORM\Entity
 */
class UjoPatch
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DATE_APPLIED", type="datetime", nullable=false)
     */
    private $dateApplied;

    /**
     * @var string
     *
     * @ORM\Column(name="DESCRIPTION", type="string", length=100, nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="ISSUE_NUMBER", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="UJO_PATCH_ISSUE_NUMBER_seq", allocationSize=1, initialValue=1)
     */
    private $issueNumber;



    /**
     * Set dateApplied
     *
     * @param \DateTime $dateApplied
     * @return UjoPatch
     */
    public function setDateApplied($dateApplied)
    {
        $this->dateApplied = $dateApplied;

        return $this;
    }

    /**
     * Get dateApplied
     *
     * @return \DateTime 
     */
    public function getDateApplied()
    {
        return $this->dateApplied;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return UjoPatch
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get issueNumber
     *
     * @return integer 
     */
    public function getIssueNumber()
    {
        return $this->issueNumber;
    }
}
