<?php

namespace Arii\ATSBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UjoGlobblob
 *
 * @ORM\Table(name="UJO_GLOBBLOB")
 * @ORM\Entity
 */
class UjoGlobblob
{
    /**
     * @var string
     *
     * @ORM\Column(name="BNAME", type="string", length=64, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="UJO_GLOBBLOB_BNAME_seq", allocationSize=1, initialValue=1)
     */
    private $bname;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="CREATE_STAMP", type="datetime", nullable=true)
     */
    private $createStamp;

    /**
     * @var string
     *
     * @ORM\Column(name="CREATE_USER", type="string", length=80, nullable=true)
     */
    private $createUser;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="MODIFY_STAMP", type="datetime", nullable=true)
     */
    private $modifyStamp;

    /**
     * @var string
     *
     * @ORM\Column(name="MODIFY_USER", type="string", length=80, nullable=true)
     */
    private $modifyUser;

    /**
     * @var string
     *
     * @ORM\Column(name="VALUE", type="blob", nullable=true)
     */
    private $value;



    /**
     * Get bname
     *
     * @return string 
     */
    public function getBname()
    {
        return $this->bname;
    }

    /**
     * Set createStamp
     *
     * @param \DateTime $createStamp
     * @return UjoGlobblob
     */
    public function setCreateStamp($createStamp)
    {
        $this->createStamp = $createStamp;

        return $this;
    }

    /**
     * Get createStamp
     *
     * @return \DateTime 
     */
    public function getCreateStamp()
    {
        return $this->createStamp;
    }

    /**
     * Set createUser
     *
     * @param string $createUser
     * @return UjoGlobblob
     */
    public function setCreateUser($createUser)
    {
        $this->createUser = $createUser;

        return $this;
    }

    /**
     * Get createUser
     *
     * @return string 
     */
    public function getCreateUser()
    {
        return $this->createUser;
    }

    /**
     * Set modifyStamp
     *
     * @param \DateTime $modifyStamp
     * @return UjoGlobblob
     */
    public function setModifyStamp($modifyStamp)
    {
        $this->modifyStamp = $modifyStamp;

        return $this;
    }

    /**
     * Get modifyStamp
     *
     * @return \DateTime 
     */
    public function getModifyStamp()
    {
        return $this->modifyStamp;
    }

    /**
     * Set modifyUser
     *
     * @param string $modifyUser
     * @return UjoGlobblob
     */
    public function setModifyUser($modifyUser)
    {
        $this->modifyUser = $modifyUser;

        return $this;
    }

    /**
     * Get modifyUser
     *
     * @return string 
     */
    public function getModifyUser()
    {
        return $this->modifyUser;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return UjoGlobblob
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }
}
