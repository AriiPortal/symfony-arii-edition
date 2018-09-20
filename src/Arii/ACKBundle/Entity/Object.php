<?php

namespace Arii\ACKBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * Object
 *
 * @ORM\Table(name="ARII_OBJECT")
 * @ORM\Entity(repositoryClass="Arii\ACKBundle\Entity\ObjectRepository")
 * 
 */
class Object
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
     * @ORM\Column(name="name", type="string", length=64)
     * @Assert\NotBlank(groups={"Create"})
     * 
     */
    private $name;

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
     * @var string
     *
     * @ORM\Column(name="obj_type", type="string", length=24)
     * 
     */
    private $obj_type;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=24)
     * 
     */
    private $source;
    
    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=12)
     * 
     */
    private $state;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=12)
     * 
     */
    private $status;

    /**
     * @var datetime
     *
     * @ORM\Column(name="state_time", type="datetime")
     * 
     */
    private $state_time;

    /**
     * @var datetime
     *
     * @ORM\Column(name="updated", type="datetime")
     * 
     */
    private $updated;
    
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
     * Set name
     *
     * @param string $name
     * @return Object
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
     * Set title
     *
     * @param string $title
     * @return Object
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
     * @return Object
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
     * Set obj_type
     *
     * @param string $objType
     * @return Object
     */
    public function setObjType($objType)
    {
        $this->obj_type = $objType;

        return $this;
    }

    /**
     * Get obj_type
     *
     * @return string 
     */
    public function getObjType()
    {
        return $this->obj_type;
    }

    /**
     * Set state
     *
     * @param string $state
     * @return Object
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return string 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set state_time
     *
     * @param \DateTime $stateTime
     * @return Object
     */
    public function setStateTime($stateTime)
    {
        $this->state_time = $stateTime;

        return $this;
    }

    /**
     * Get state_time
     *
     * @return \DateTime 
     */
    public function getStateTime()
    {
        return $this->state_time;
    }

    /**
     * Set source
     *
     * @param string $source
     * @return Object
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return string 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Object
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Object
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }
}
