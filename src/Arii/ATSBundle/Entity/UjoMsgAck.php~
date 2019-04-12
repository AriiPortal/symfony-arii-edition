<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UjoMsgAck
 *
 * @ORM\Table(name="UJO_MSG_ACK")
 * @ORM\Entity
 */
class UjoMsgAck
{
    /**
     * @var string
     *
     * @ORM\Column(name="COMM", type="string", length=80, nullable=true)
     */
    private $comm;

    /**
     * @var string
     *
     * @ORM\Column(name="EOID", type="string", length=12, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="UJO_MSG_ACK_EOID_seq", allocationSize=1, initialValue=1)
     */
    private $eoid;

    /**
     * @var integer
     *
     * @ORM\Column(name="TIMEACK", type="integer", nullable=true)
     */
    private $timeack;

    /**
     * @var integer
     *
     * @ORM\Column(name="TIMEIN", type="integer", nullable=true)
     */
    private $timein;

    /**
     * @var string
     *
     * @ORM\Column(name="WHO", type="string", length=30, nullable=true)
     */
    private $who;



    /**
     * Set comm
     *
     * @param string $comm
     * @return UjoMsgAck
     */
    public function setComm($comm)
    {
        $this->comm = $comm;

        return $this;
    }

    /**
     * Get comm
     *
     * @return string 
     */
    public function getComm()
    {
        return $this->comm;
    }

    /**
     * Get eoid
     *
     * @return string 
     */
    public function getEoid()
    {
        return $this->eoid;
    }

    /**
     * Set timeack
     *
     * @param integer $timeack
     * @return UjoMsgAck
     */
    public function setTimeack($timeack)
    {
        $this->timeack = $timeack;

        return $this;
    }

    /**
     * Get timeack
     *
     * @return integer 
     */
    public function getTimeack()
    {
        return $this->timeack;
    }

    /**
     * Set timein
     *
     * @param integer $timein
     * @return UjoMsgAck
     */
    public function setTimein($timein)
    {
        $this->timein = $timein;

        return $this;
    }

    /**
     * Get timein
     *
     * @return integer 
     */
    public function getTimein()
    {
        return $this->timein;
    }

    /**
     * Set who
     *
     * @param string $who
     * @return UjoMsgAck
     */
    public function setWho($who)
    {
        $this->who = $who;

        return $this;
    }

    /**
     * Get who
     *
     * @return string 
     */
    public function getWho()
    {
        return $this->who;
    }
}
