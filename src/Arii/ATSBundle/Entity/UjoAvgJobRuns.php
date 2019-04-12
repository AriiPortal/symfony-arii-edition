<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UjoAvgJobRuns
 *
 * @ORM\Table(name="UJO_AVG_JOB_RUNS")
 * @ORM\Entity
 */
class UjoAvgJobRuns
{
    /**
     * @var integer
     *
     * @ORM\Column(name="AVG_RUN_TIME", type="integer", nullable=true)
     */
    private $avgRunTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="JOID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="UJO_AVG_JOB_RUNS_JOID_seq", allocationSize=1, initialValue=1)
     */
    private $joid;

    /**
     * @var integer
     *
     * @ORM\Column(name="NUM_RUNS", type="integer", nullable=true)
     */
    private $numRuns;



    /**
     * Set avgRunTime
     *
     * @param integer $avgRunTime
     * @return UjoAvgJobRuns
     */
    public function setAvgRunTime($avgRunTime)
    {
        $this->avgRunTime = $avgRunTime;

        return $this;
    }

    /**
     * Get avgRunTime
     *
     * @return integer 
     */
    public function getAvgRunTime()
    {
        return $this->avgRunTime;
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
     * Set numRuns
     *
     * @param integer $numRuns
     * @return UjoAvgJobRuns
     */
    public function setNumRuns($numRuns)
    {
        $this->numRuns = $numRuns;

        return $this;
    }

    /**
     * Get numRuns
     *
     * @return integer 
     */
    public function getNumRuns()
    {
        return $this->numRuns;
    }
}
