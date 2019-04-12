<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UjoJobtype
 *
 * @ORM\Table(name="UJO_JOBTYPE")
 * @ORM\Entity
 */
class UjoJobtype
{
    /**
     * @var string
     *
     * @ORM\Column(name="DESCRIPTION", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="EXE_NAME", type="string", length=255, nullable=true)
     */
    private $exeName;

    /**
     * @var integer
     *
     * @ORM\Column(name="JOB_TYPE", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="UJO_JOBTYPE_JOB_TYPE_seq", allocationSize=1, initialValue=1)
     */
    private $jobType;

    /**
     * @var string
     *
     * @ORM\Column(name="STAGE_BLOB", type="string", length=1, nullable=true)
     */
    private $stageBlob;



    /**
     * Set description
     *
     * @param string $description
     * @return UjoJobtype
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
     * Set exeName
     *
     * @param string $exeName
     * @return UjoJobtype
     */
    public function setExeName($exeName)
    {
        $this->exeName = $exeName;

        return $this;
    }

    /**
     * Get exeName
     *
     * @return string 
     */
    public function getExeName()
    {
        return $this->exeName;
    }

    /**
     * Get jobType
     *
     * @return integer 
     */
    public function getJobType()
    {
        return $this->jobType;
    }

    /**
     * Set stageBlob
     *
     * @param string $stageBlob
     * @return UjoJobtype
     */
    public function setStageBlob($stageBlob)
    {
        $this->stageBlob = $stageBlob;

        return $this;
    }

    /**
     * Get stageBlob
     *
     * @return string 
     */
    public function getStageBlob()
    {
        return $this->stageBlob;
    }
}
