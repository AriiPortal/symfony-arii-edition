<?php

namespace Arii\JIDBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SchedulerJobChainNodes
 *
 * @ORM\Table(name="scheduler_job_chain_nodes")
 * @ORM\Entity(readOnly=true,repositoryClass="Arii\JIDBundle\Entity\SchedulerJobChainNodesRepository")
 */
class SchedulerJobChainNodes
{
    /**
     * @var string
     *
     * @ORM\Column(name="SPOOLER_ID", type="string", length=100, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $spoolerId;

    /**
     * @var string
     *
     * @ORM\Column(name="CLUSTER_MEMBER_ID", type="string", length=100, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $clusterMemberId;

    /**
     * @var string
     *
     * @ORM\Column(name="JOB_CHAIN", type="string", length=255, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $jobChain;

    /**
     * @var string
     *
     * @ORM\Column(name="ORDER_STATE", type="string", length=100, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $orderState;

    /**
     * @var string
     *
     * @ORM\Column(name="ACTION", type="string", length=100, nullable=true)
     */
    private $action;



    /**
     * Set spoolerId
     *
     * @param string $spoolerId
     * @return SchedulerJobChainNodes
     */
    public function setSpoolerId($spoolerId)
    {
        $this->spoolerId = $spoolerId;

        return $this;
    }

    /**
     * Get spoolerId
     *
     * @return string 
     */
    public function getSpoolerId()
    {
        return $this->spoolerId;
    }

    /**
     * Set clusterMemberId
     *
     * @param string $clusterMemberId
     * @return SchedulerJobChainNodes
     */
    public function setClusterMemberId($clusterMemberId)
    {
        $this->clusterMemberId = $clusterMemberId;

        return $this;
    }

    /**
     * Get clusterMemberId
     *
     * @return string 
     */
    public function getClusterMemberId()
    {
        return $this->clusterMemberId;
    }

    /**
     * Set jobChain
     *
     * @param string $jobChain
     * @return SchedulerJobChainNodes
     */
    public function setJobChain($jobChain)
    {
        $this->jobChain = $jobChain;

        return $this;
    }

    /**
     * Get jobChain
     *
     * @return string 
     */
    public function getJobChain()
    {
        return $this->jobChain;
    }

    /**
     * Set orderState
     *
     * @param string $orderState
     * @return SchedulerJobChainNodes
     */
    public function setOrderState($orderState)
    {
        $this->orderState = $orderState;

        return $this;
    }

    /**
     * Get orderState
     *
     * @return string 
     */
    public function getOrderState()
    {
        return $this->orderState;
    }

    /**
     * Set action
     *
     * @param string $action
     * @return SchedulerJobChainNodes
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }
}
