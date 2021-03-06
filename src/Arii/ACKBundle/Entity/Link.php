<?php

namespace Arii\ACKBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * Link
 *
 * @ORM\Table(name="ARII_PROBE__PROBE")
 * @ORM\Entity(repositoryClass="Arii\ACKBundle\Entity\LinkRepository")
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
     * @var string
     * 
    * 0: Aucun lien (lien existant mais désactivé)
    * 1: from CONTAINS to (to est dans from)
    * 2: from DEPENDS on (from depend de to)
    * Utile ? 
    * -- 3: from IS IN to (from est dans to)
    * -- 4: from IMPACTS to (to depend de from)
     * 
     * @ORM\Column(name="link_type", type="integer")
     * 
     */
    private $link_type=0;

    /**
     * @var integer
     * 
     * 
     * @ORM\Column(name="link_strength", type="integer")
     * 
     */
    private $link_strength;

    /**
    * @ORM\ManyToOne(targetEntity="Arii\ACKBundle\Entity\Probe")
    * @ORM\JoinColumn()
    */
    private $obj_from;
    
    /**
    * @ORM\ManyToOne(targetEntity="Arii\ACKBundle\Entity\Probe")
    * @ORM\JoinColumn(nullable=true)
    */
    private $obj_to;

    /**
     * @var datetime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=true)
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
     * Set link_type
     *
     * @param string $linkType
     * @return Link
     */
    public function setLinkType($linkType)
    {
        $this->link_type = $linkType;

        return $this;
    }

    /**
     * Get link_type
     *
     * @return string 
     */
    public function getLinkType()
    {
        return $this->link_type;
    }

    /**
     * Set obj_from
     *
     * @param \Arii\ACKBundle\Entity\Probe $objFrom
     * @return Link
     */
    public function setObjFrom(\Arii\ACKBundle\Entity\Probe $objFrom = null)
    {
        $this->obj_from = $objFrom;

        return $this;
    }

    /**
     * Get obj_from
     *
     * @return \Arii\ACKBundle\Entity\Probe 
     */
    public function getObjFrom()
    {
        return $this->obj_from;
    }

    /**
     * Set obj_to
     *
     * @param \Arii\ACKBundle\Entity\Probe $objTo
     * @return Link
     */
    public function setObjTo(\Arii\ACKBundle\Entity\Probe $objTo = null)
    {
        $this->obj_to = $objTo;

        return $this;
    }

    /**
     * Get obj_to
     *
     * @return \Arii\ACKBundle\Entity\Probe 
     */
    public function getObjTo()
    {
        return $this->obj_to;
    }

    /**
     * Set link_strength
     *
     * @param integer $linkStrength
     * @return Link
     */
    public function setLinkStrength($linkStrength)
    {
        $this->link_strength = $linkStrength;

        return $this;
    }

    /**
     * Get link_strength
     *
     * @return integer 
     */
    public function getLinkStrength()
    {
        return $this->link_strength;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Link
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
}
