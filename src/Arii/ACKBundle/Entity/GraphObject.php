<?php

namespace Arii\ACKBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * Object
 *
 * @ORM\Table(name="ARII_GRAPH__OBJECT")
 * @ORM\Entity(repositoryClass="Arii\ACKBundle\Entity\GraphObjectRepository")
 * 
 */
class GraphObject
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
     * @ORM\ManyToOne(targetEntity="Arii\ACKBundle\Entity\Object")
     * @ORM\JoinColumn(nullable=true)
     **/
    private $object;

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
     * @return GraphObject
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
     * @return GraphObject
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
     * @return GraphObject
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
     * Set object
     *
     * @param \Arii\ACKBundle\Entity\Object $object
     * @return GraphObject
     */
    public function setObject(\Arii\ACKBundle\Entity\Object $object = null)
    {
        $this->object = $object;

        return $this;
    }

    /**
     * Get object
     *
     * @return \Arii\ACKBundle\Entity\Object 
     */
    public function getObject()
    {
        return $this->object;
    }
}
