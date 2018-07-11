<?php

namespace Arii\SelfBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Self-Service
 *
 * @ORM\Table(name="SELF_REQUEST")
 * @ORM\Entity(repositoryClass="Arii\SelfBundle\Entity\RequestRepository")
 */
class Request
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255,nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="template", type="boolean")
     */
    private $template=0;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=255,nullable=true)
     */
    private $reference;
        
    /**
     * @ORM\ManyToOne(targetEntity="Arii\UserBundle\Entity\User")
     */
    private $requester;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=64, nullable=true)
     */
    private $username;
    
    /**
     * @var string
     *
     * @ORM\Column(name="run_command", type="string", length=1024, nullable=true)
     */
    private $run_command;

    /**
     * @var string
     *
     * @ORM\Column(name="run_type", type="string", length=12, nullable=true)
     */
    private $run_type='C';

    /**
     * @var string
     *
     * @ORM\Column(name="parameters", type="array", nullable=true)
     */
    private $parameters;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="planned", type="datetime", nullable=true)
     */
    private $planned;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="processed", type="datetime", nullable=true)
     */
    private $processed;

    /**
     * @var string
     *
     * @ORM\Column(name="req_status", type="string", length=12, nullable=true)
     */
    private $req_status;
    
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
     * @return Request
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
     * @return Request
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
     * @return Request
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
     * Set reference
     *
     * @param string $reference
     * @return Request
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string 
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return Request
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set run_command
     *
     * @param string $runCommand
     * @return Request
     */
    public function setRunCommand($runCommand)
    {
        $this->run_command = $runCommand;

        return $this;
    }

    /**
     * Get run_command
     *
     * @return string 
     */
    public function getRunCommand()
    {
        return $this->run_command;
    }

    /**
     * Set run_type
     *
     * @param string $runType
     * @return Request
     */
    public function setRunType($runType)
    {
        $this->run_type = $runType;

        return $this;
    }

    /**
     * Get run_type
     *
     * @return string 
     */
    public function getRunType()
    {
        return $this->run_type;
    }

    /**
     * Set parameters
     *
     * @param array $parameters
     * @return Request
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Get parameters
     *
     * @return array 
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Request
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set planned
     *
     * @param \DateTime $planned
     * @return Request
     */
    public function setPlanned($planned)
    {
        $this->planned = $planned;

        return $this;
    }

    /**
     * Get planned
     *
     * @return \DateTime 
     */
    public function getPlanned()
    {
        return $this->planned;
    }

    /**
     * Set requester
     *
     * @param \Arii\UserBundle\Entity\User $requester
     * @return Request
     */
    public function setRequester(\Arii\UserBundle\Entity\User $requester = null)
    {
        $this->requester = $requester;

        return $this;
    }

    /**
     * Get requester
     *
     * @return \Arii\UserBundle\Entity\User 
     */
    public function getRequester()
    {
        return $this->requester;
    }

    /**
     * Set processed
     *
     * @param \DateTime $processed
     * @return Request
     */
    public function setProcessed($processed)
    {
        $this->processed = $processed;

        return $this;
    }

    /**
     * Get processed
     *
     * @return \DateTime 
     */
    public function getProcessed()
    {
        return $this->processed;
    }

    /**
     * Set req_status
     *
     * @param string $reqStatus
     * @return Request
     */
    public function setReqStatus($reqStatus)
    {
        $this->req_status = $reqStatus;

        return $this;
    }

    /**
     * Get req_status
     *
     * @return string 
     */
    public function getReqStatus()
    {
        return $this->req_status;
    }
}
