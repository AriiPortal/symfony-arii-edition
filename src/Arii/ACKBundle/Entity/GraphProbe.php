<?php

namespace Arii\ACKBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * Probe
 *
 * @ORM\Table(name="ARII_GRAPH__PROBE")
 * @ORM\Entity(repositoryClass="Arii\ACKBundle\Entity\GraphProbeRepository")
 * 
 */
class GraphProbe
{
    public function __construct()
    {
    }
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     * @Serializer\Groups({"list","detail"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=64)
     * 
     * @Serializer\Groups({"list"})
     */
    private $title;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     * @Assert\NotBlank(groups={"Create"})
     * 
     * @Serializer\Groups({"detail"})
     */
    private $description;    

    /**
     * @ORM\ManyToOne(targetEntity="Arii\ACKBundle\Entity\Graph")
     * @ORM\JoinColumn(nullable=true)
     **/
    private $graph;
    
    /**
     * @ORM\ManyToOne(targetEntity="Arii\ACKBundle\Entity\Probe")
     * @ORM\JoinColumn(nullable=true)
     **/
    private $Probe;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return GraphProbe
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return GraphProbe
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
     * Set graph
     *
     * @param \Arii\ACKBundle\Entity\Graph $graph
     * @return GraphProbe
     */
    public function setGraph(\Arii\ACKBundle\Entity\Graph $graph = null)
    {
        $this->graph = $graph;

        return $this;
    }

    /**
     * Get graph
     *
     * @return \Arii\ACKBundle\Entity\Graph 
     */
    public function getGraph()
    {
        return $this->graph;
    }

    /**
     * Set Probe
     *
     * @param \Arii\ACKBundle\Entity\Probe $Probe
     * @return GraphProbe
     */
    public function setProbe(\Arii\ACKBundle\Entity\Probe $Probe = null)
    {
        $this->Probe = $Probe;

        return $this;
    }

    /**
     * Get Probe
     *
     * @return \Arii\ACKBundle\Entity\Probe 
     */
    public function getProbe()
    {
        return $this->Probe;
    }
}
