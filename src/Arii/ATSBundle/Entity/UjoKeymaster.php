<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UjoKeymaster
 *
 * @ORM\Table(name="UJO_KEYMASTER")
 * @ORM\Entity
 */
class UjoKeymaster
{
    /**
     * @var string
     *
     * @ORM\Column(name="DAKEY", type="string", length=512, nullable=false)
     */
    private $dakey;

    /**
     * @var string
     *
     * @ORM\Column(name="HOSTID", type="string", length=32, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $hostid;

    /**
     * @var string
     *
     * @ORM\Column(name="HOSTNAME", type="string", length=256, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $hostname;

    /**
     * @var string
     *
     * @ORM\Column(name="NOT_USED", type="string", length=16, nullable=false)
     */
    private $notUsed;

    /**
     * @var string
     *
     * @ORM\Column(name="PRODUCT", type="string", length=31, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $product;

    /**
     * @var string
     *
     * @ORM\Column(name="SERVER", type="string", length=12, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $server;

    /**
     * @var string
     *
     * @ORM\Column(name="TYPE", type="string", length=1, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $type;



    /**
     * Set dakey
     *
     * @param string $dakey
     * @return UjoKeymaster
     */
    public function setDakey($dakey)
    {
        $this->dakey = $dakey;

        return $this;
    }

    /**
     * Get dakey
     *
     * @return string 
     */
    public function getDakey()
    {
        return $this->dakey;
    }

    /**
     * Set hostid
     *
     * @param string $hostid
     * @return UjoKeymaster
     */
    public function setHostid($hostid)
    {
        $this->hostid = $hostid;

        return $this;
    }

    /**
     * Get hostid
     *
     * @return string 
     */
    public function getHostid()
    {
        return $this->hostid;
    }

    /**
     * Set hostname
     *
     * @param string $hostname
     * @return UjoKeymaster
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;

        return $this;
    }

    /**
     * Get hostname
     *
     * @return string 
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Set notUsed
     *
     * @param string $notUsed
     * @return UjoKeymaster
     */
    public function setNotUsed($notUsed)
    {
        $this->notUsed = $notUsed;

        return $this;
    }

    /**
     * Get notUsed
     *
     * @return string 
     */
    public function getNotUsed()
    {
        return $this->notUsed;
    }

    /**
     * Set product
     *
     * @param string $product
     * @return UjoKeymaster
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return string 
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set server
     *
     * @param string $server
     * @return UjoKeymaster
     */
    public function setServer($server)
    {
        $this->server = $server;

        return $this;
    }

    /**
     * Get server
     *
     * @return string 
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return UjoKeymaster
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }
}
