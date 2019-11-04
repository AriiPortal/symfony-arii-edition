<?php

namespace Arii\MFTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Connections
 *
 * @ORM\Table(name="MFT_SUMMARY")
 * @ORM\Entity(readOnly=true,repositoryClass="Arii\MFTBundle\Entity\SummaryRepository")
 */
class Summary
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64)
     */        
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */        
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */        
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=64, nullable=true)
     */        
    private $source;

    /**
     * @var string
     *
     * @ORM\Column(name="target", type="string", length=64, nullable=true)
     */        
    private $target;

    /**
     * @var string
     *
     * @ORM\Column(name="operation", type="string", length=64, nullable=true)
     */        
    private $operation;

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="string", length=64, nullable=true)
     */        
    private $data;
    
    /**
     * @var string
     *
     * @ORM\Column(name="protocol", type="string", length=64, nullable=true)
     */        
    private $protocol;
    
    /**
    * @ORM\ManyToOne(targetEntity="Arii\MFTBundle\Entity\Partners")
    * @ORM\JoinColumn(nullable=true)
    */
    private $partner;
    
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
     * Set source
     *
     * @param string $source
     * @return Summary
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
     * Set target
     *
     * @param string $target
     * @return Summary
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return string 
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set operation
     *
     * @param string $operation
     * @return Summary
     */
    public function setOperation($operation)
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * Get operation
     *
     * @return string 
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * Set data
     *
     * @param string $data
     * @return Summary
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set protocol
     *
     * @param string $protocol
     * @return Summary
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;

        return $this;
    }

    /**
     * Get protocol
     *
     * @return string 
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Summary
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
     * @return Summary
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
     * @return Summary
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
     * Set partner
     *
     * @param \Arii\MFTBundle\Entity\Partners $partner
     * @return Summary
     */
    public function setPartner(\Arii\MFTBundle\Entity\Partners $partner = null)
    {
        $this->partner = $partner;

        return $this;
    }

    /**
     * Get partner
     *
     * @return \Arii\MFTBundle\Entity\Partners 
     */
    public function getPartner()
    {
        return $this->partner;
    }
}
