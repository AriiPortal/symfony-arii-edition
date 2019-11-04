<?php

namespace Arii\MFTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Operations
 *
 * @ORM\Table(name="MFT_OPERATIONS")
 * @ORM\Entity(readOnly=true,repositoryClass="Arii\MFTBundle\Entity\OperationsRepository")
 */
class Operations
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
    * @ORM\ManyToOne(targetEntity="Arii\MFTBundle\Entity\Transfers")
    * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
    */
    private $transfer;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64)
     */        
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="env", type="string", length=64, nullable=true)
     */        
    private $env;

    /**
     * @var string
     *
     * @ORM\Column(name="step", type="string", length=32, nullable=true )
     */        
    private $step;

    /**
     * @var integer
     *
     * @ORM\Column(name="ordering", type="integer", nullable=true )
     */
    private $ordering;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=512, nullable=true)
     */        
    private $title;
    
    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=512, nullable=true)
     */        
    private $description;

     /**
     * @ORM\ManyToOne(targetEntity="Arii\CoreBundle\Entity\Connection")
     * @ORM\JoinColumn(nullable=true)
     **/
    private $source;

     /**
     * @ORM\ManyToOne(targetEntity="Arii\CoreBundle\Entity\Connection")
     * @ORM\JoinColumn(nullable=true)
     **/
    private $target;

    /**
     * @var string
     *
     * @ORM\Column(name="source_path", type="string", length=512, nullable=true)
     */
    private $source_path;

    /**
     * @var string
     *
     * @ORM\Column(name="target_path", type="string", length=512, nullable=true)
     */
    private $target_path;

    /**
     * @var string
     *
     * @ORM\Column(name="source_files", type="string", length=128, nullable=true)
     */
    private $source_files;

    /**
     * @var string
     *
     * @ORM\Column(name="target_files", type="string", length=128, nullable=true)
     */        
    private $target_files;

    /**
     * @var string
     *
     * @ORM\Column(name="client", type="string", length=16, nullable=true)
     */        
    private $client;
    
    /**
    * @ORM\ManyToOne(targetEntity="Arii\MFTBundle\Entity\Parameters")
    * @ORM\JoinColumn(nullable=true)
    */
    private $parameters;

    /**
     * @var integer
     *
     * @ORM\Column(name="expected_files", type="integer", nullable=true )
     */        
    private $expected_files;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="exit_if_nothing", type="integer", nullable = true )
     */        
    private $exit_if_nothing;

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
     * @return Operations
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
     * Set env
     *
     * @param string $env
     * @return Operations
     */
    public function setEnv($env)
    {
        $this->env = $env;

        return $this;
    }

    /**
     * Get env
     *
     * @return string 
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * Set step
     *
     * @param integer $step
     * @return Operations
     */
    public function setStep($step)
    {
        $this->step = $step;

        return $this;
    }

    /**
     * Get step
     *
     * @return integer 
     */
    public function getStep()
    {
        return $this->step;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Operations
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
     * @return Operations
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
     * Set source_path
     *
     * @param string $sourcePath
     * @return Operations
     */
    public function setSourcePath($sourcePath)
    {
        $this->source_path = $sourcePath;

        return $this;
    }

    /**
     * Get source_path
     *
     * @return string 
     */
    public function getSourcePath()
    {
        return $this->source_path;
    }

    /**
     * Set target_path
     *
     * @param string $targetPath
     * @return Operations
     */
    public function setTargetPath($targetPath)
    {
        $this->target_path = $targetPath;

        return $this;
    }

    /**
     * Get target_path
     *
     * @return string 
     */
    public function getTargetPath()
    {
        return $this->target_path;
    }

    /**
     * Set source_files
     *
     * @param string $sourceFiles
     * @return Operations
     */
    public function setSourceFiles($sourceFiles)
    {
        $this->source_files = $sourceFiles;

        return $this;
    }

    /**
     * Get source_files
     *
     * @return string 
     */
    public function getSourceFiles()
    {
        return $this->source_files;
    }

    /**
     * Set target_files
     *
     * @param string $targetFiles
     * @return Operations
     */
    public function setTargetFiles($targetFiles)
    {
        $this->target_files = $targetFiles;

        return $this;
    }

    /**
     * Get target_files
     *
     * @return string 
     */
    public function getTargetFiles()
    {
        return $this->target_files;
    }

    /**
     * Set client
     *
     * @param string $client
     * @return Operations
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return string 
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set transfer
     *
     * @param \Arii\MFTBundle\Entity\Transfers $transfer
     * @return Operations
     */
    public function setTransfer(\Arii\MFTBundle\Entity\Transfers $transfer = null)
    {
        $this->transfer = $transfer;

        return $this;
    }

    /**
     * Get transfer
     *
     * @return \Arii\MFTBundle\Entity\Transfers 
     */
    public function getTransfer()
    {
        return $this->transfer;
    }

    /**
     * Set source
     *
     * @param \Arii\CoreBundle\Entity\Connection $source
     * @return Operations
     */
    public function setSource(\Arii\CoreBundle\Entity\Connection $source = null)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return \Arii\CoreBundle\Entity\Connection 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set target
     *
     * @param \Arii\CoreBundle\Entity\Connection $target
     * @return Operations
     */
    public function setTarget(\Arii\CoreBundle\Entity\Connection $target = null)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return \Arii\CoreBundle\Entity\Connection 
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set parameters
     *
     * @param \Arii\MFTBundle\Entity\Parameters $parameters
     * @return Operations
     */
    public function setParameters(\Arii\MFTBundle\Entity\Parameters $parameters = null)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Get parameters
     *
     * @return \Arii\MFTBundle\Entity\Parameters 
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Set ordering
     *
     * @param integer $ordering
     * @return Operations
     */
    public function setOrdering($ordering)
    {
        $this->ordering = $ordering;

        return $this;
    }

    /**
     * Get ordering
     *
     * @return integer 
     */
    public function getOrdering()
    {
        return $this->ordering;
    }

    /**
     * Set expected_files
     *
     * @param integer $expectedFiles
     * @return Operations
     */
    public function setExpectedFiles($expectedFiles)
    {
        $this->expected_files = $expectedFiles;

        return $this;
    }

    /**
     * Get expected_files
     *
     * @return integer 
     */
    public function getExpectedFiles()
    {
        return $this->expected_files;
    }

    /**
     * Set exit_if_nothing
     *
     * @param integer $exitIfNothing
     * @return Operations
     */
    public function setExitIfNothing($exitIfNothing)
    {
        $this->exit_if_nothing = $exitIfNothing;

        return $this;
    }

    /**
     * Get exit_if_nothing
     *
     * @return integer 
     */
    public function getExitIfNothing()
    {
        return $this->exit_if_nothing;
    }
}
