<?php

namespace Arii\MFTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Connections
 *
 * @ORM\Table(name="MFT_TRANSMISSIONS")
 * @ORM\Entity(readOnly=true,repositoryClass="Arii\MFTBundle\Entity\TransmissionsRepository")
 */
class Transmissions
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
     * @ORM\ManyToOne(targetEntity="Arii\MFTBundle\Entity\Deliveries")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     **/
    private $delivery; 

    /**
     * @var string
     *
     * @ORM\Column(name="source_file", type="string", length=128)
     */        
    private $source_file;

    /**
     * @var string
     *
     * @ORM\Column(name="target_file", type="string", length=128)
     */        
    private $target_file;

    /**
     * @var datetime
     *
     * @ORM\Column(name="start_time", type="datetime" )
     */        
    private $start_time;

    /**
     * @var datetime
     *
     * @ORM\Column(name="end_time", type="datetime" )
     */        
    private $end_time;

    /**
     * @var integer
     *
     * @ORM\Column(name="duration", type="integer" )
     */        
    private $duration;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=16)
     */        
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="pid", type="integer", nullable=true)
     */
    private $pid;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="file_size", type="integer")
     */
    private $file_size;

    /**
     * @var string
     *
     * @ORM\Column(name="md5", type="string", length=32, nullable=true )
     */
    private $md5;


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
     * Set source_file
     *
     * @param string $sourceFile
     * @return Transmissions
     */
    public function setSourceFile($sourceFile)
    {
        $this->source_file = $sourceFile;

        return $this;
    }

    /**
     * Get source_file
     *
     * @return string 
     */
    public function getSourceFile()
    {
        return $this->source_file;
    }

    /**
     * Set target_file
     *
     * @param string $targetFile
     * @return Transmissions
     */
    public function setTargetFile($targetFile)
    {
        $this->target_file = $targetFile;

        return $this;
    }

    /**
     * Get target_file
     *
     * @return string 
     */
    public function getTargetFile()
    {
        return $this->target_file;
    }

    /**
     * Set start_time
     *
     * @param \DateTime $startTime
     * @return Transmissions
     */
    public function setStartTime($startTime)
    {
        $this->start_time = $startTime;

        return $this;
    }

    /**
     * Get start_time
     *
     * @return \DateTime 
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * Set end_time
     *
     * @param \DateTime $endTime
     * @return Transmissions
     */
    public function setEndTime($endTime)
    {
        $this->end_time = $endTime;

        return $this;
    }

    /**
     * Get end_time
     *
     * @return \DateTime 
     */
    public function getEndTime()
    {
        return $this->end_time;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     * @return Transmissions
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer 
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Transmissions
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

    /**
     * Set pid
     *
     * @param integer $pid
     * @return Transmissions
     */
    public function setPid($pid)
    {
        $this->pid = $pid;

        return $this;
    }

    /**
     * Get pid
     *
     * @return integer 
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Set file_size
     *
     * @param integer $fileSize
     * @return Transmissions
     */
    public function setFileSize($fileSize)
    {
        $this->file_size = $fileSize;

        return $this;
    }

    /**
     * Get file_size
     *
     * @return integer 
     */
    public function getFileSize()
    {
        return $this->file_size;
    }

    /**
     * Set md5
     *
     * @param string $md5
     * @return Transmissions
     */
    public function setMd5($md5)
    {
        $this->md5 = $md5;

        return $this;
    }

    /**
     * Get md5
     *
     * @return string 
     */
    public function getMd5()
    {
        return $this->md5;
    }

    /**
     * Set delivery
     *
     * @param \Arii\MFTBundle\Entity\Deliveries $delivery
     * @return Transmissions
     */
    public function setDelivery(\Arii\MFTBundle\Entity\Deliveries $delivery = null)
    {
        $this->delivery = $delivery;

        return $this;
    }

    /**
     * Get delivery
     *
     * @return \Arii\MFTBundle\Entity\Deliveries 
     */
    public function getDelivery()
    {
        return $this->delivery;
    }
}
