<?php

namespace Arii\ACKBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * Link
 *
 * @ORM\Table(name="ARII_LINK")
 * @ORM\Entity(repositoryClass="Arii\ACKBundle\Entity\AlarmRepository")
 * 
 */
class Link
{
    public function __construct()
    {
        $this->alarmTime = new \DateTime();
        $this->stateTime = new \DateTime();
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
    * @ORM\ManyToOne(targetEntity="Arii\ACKBundle\Entity\Status")
    * @ORM\JoinColumn(nullable=true)
    */
    private $from;

    /**
    * @ORM\ManyToOne(targetEntity="Arii\ACKBundle\Entity\Status")
    * @ORM\JoinColumn(nullable=true)
    */
    private $to;

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
     * @return Link
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
     * @return Link
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
     * @return Link
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
     * Set from
     *
     * @param \Arii\ACKBundle\Entity\Status $from
     * @return Link
     */
    public function setFrom(\Arii\ACKBundle\Entity\Status $from = null)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Get from
     *
     * @return \Arii\ACKBundle\Entity\Status 
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * Set to
     *
     * @param \Arii\ACKBundle\Entity\Status $to
     * @return Link
     */
    public function setTo(\Arii\ACKBundle\Entity\Status $to = null)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Get to
     *
     * @return \Arii\ACKBundle\Entity\Status 
     */
    public function getTo()
    {
        return $this->to;
    }
}
