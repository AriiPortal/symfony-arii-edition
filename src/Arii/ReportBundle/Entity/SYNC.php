<?php

namespace Arii\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SYNC
 *
 * @ORM\Table(name="REPORT_SYNC")
 * @ORM\Entity
 */
class SYNC
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
     * @ORM\Column(name="name", type="string", length=128)
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="db_name", type="string", length=32)
     */
    private $db_name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="db_entity", type="string", length=64)
     */
    private $entity;

    /**
     * @var bigint
     *
     * @ORM\Column(name="last_id", type="bigint")
     */
    private $last_id;

    /**
     * @var string
     *
     * @ORM\Column(name="check_point", type="string", length=32)
     */
    private $check_point;

    /**
     * @var datetime
     *
     * @ORM\Column(name="last_update", type="datetime")
     */
    private $last_update;

    /**
     * @var integer
     *
     * @ORM\Column(name="duration", type="integer")
     */
    private $duration;
    
    /**
     * @var string
     *
     * @ORM\Column(name="route", type="string", length=32)
     */
    private $route;

    /**
     * @var interger
     *
     * @ORM\Column(name="nb_lines", type="integer")
     */
    private $nb_lines;

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
     * @return SYNC
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
     * Set db_name
     *
     * @param string $dbName
     * @return SYNC
     */
    public function setDbName($dbName)
    {
        $this->db_name = $dbName;

        return $this;
    }

    /**
     * Get db_name
     *
     * @return string 
     */
    public function getDbName()
    {
        return $this->db_name;
    }

    /**
     * Set entity
     *
     * @param string $entity
     * @return SYNC
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return string 
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set last_id
     *
     * @param integer $lastId
     * @return SYNC
     */
    public function setLastId($lastId)
    {
        $this->last_id = $lastId;

        return $this;
    }

    /**
     * Get last_id
     *
     * @return integer 
     */
    public function getLastId()
    {
        return $this->last_id;
    }

    /**
     * Set check_point
     *
     * @param string $checkPoint
     * @return SYNC
     */
    public function setCheckPoint($checkPoint)
    {
        $this->check_point = $checkPoint;

        return $this;
    }

    /**
     * Get check_point
     *
     * @return string 
     */
    public function getCheckPoint()
    {
        return $this->check_point;
    }

    /**
     * Set last_update
     *
     * @param \DateTime $lastUpdate
     * @return SYNC
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->last_update = $lastUpdate;

        return $this;
    }

    /**
     * Get last_update
     *
     * @return \DateTime 
     */
    public function getLastUpdate()
    {
        return $this->last_update;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     * @return SYNC
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
     * Set route
     *
     * @param string $route
     * @return SYNC
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return string 
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set nb_lines
     *
     * @param integer $nbLines
     * @return SYNC
     */
    public function setNbLines($nbLines)
    {
        $this->nb_lines = $nbLines;

        return $this;
    }

    /**
     * Get nb_lines
     *
     * @return integer 
     */
    public function getNbLines()
    {
        return $this->nb_lines;
    }
}
