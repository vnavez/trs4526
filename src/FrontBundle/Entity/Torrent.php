<?php

namespace FrontBundle\Entity;

/**
 * Torrent
 */
class Torrent
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $idT411;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $status;

    /**
     * @var string
     */
    private $category;

    /**
     * @var string
     */
    private $file;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idT411
     *
     * @param integer $idT411
     *
     * @return Torrent
     */
    public function setIdT411($idT411)
    {
        $this->idT411 = $idT411;

        return $this;
    }

    /**
     * Get idT411
     *
     * @return int
     */
    public function getIdT411()
    {
        return $this->idT411;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Torrent
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
     * Set status
     *
     * @param integer $status
     *
     * @return Torrent
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }
    /**
     * @var integer
     */
    private $idTransmission;


    /**
     * Set idTransmission
     *
     * @param integer $idTransmission
     *
     * @return Torrent
     */
    public function setIdTransmission($idTransmission)
    {
        $this->idTransmission = $idTransmission;

        return $this;
    }

    /**
     * Get idTransmission
     *
     * @return integer
     */
    public function getIdTransmission()
    {
        return $this->idTransmission;
    }
    /**
     * @var integer
     */
    private $idUser;

    /**
     * @var \FrontBundle\Entity\User
     */
    private $user;


    /**
     * Set idUser
     *
     * @param integer $idUser
     *
     * @return Torrent
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Get idUser
     *
     * @return integer
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set user
     *
     * @param \FrontBundle\Entity\User $user
     *
     * @return Torrent
     */
    public function setUser(\FrontBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \FrontBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set category
     *
     * @param string $category
     *
     * @return Torrent
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }
    /**
     * @var \DateTime
     */
    private $date_add;


    /**
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     *
     * @return Torrent
     */
    public function setDateAdd($dateAdd)
    {
        $this->date_add = $dateAdd;

        return $this;
    }

    /**
     * Get dateAdd
     *
     * @return \DateTime
     */
    public function getDateAdd()
    {
        return $this->date_add;
    }
    /**
     * @var \DateTime
     */
    private $date_upd;


    /**
     * Set dateUpd
     *
     * @param \DateTime $dateUpd
     *
     * @return Torrent
     */
    public function setDateUpd($dateUpd)
    {
        $this->date_upd = $dateUpd;

        return $this;
    }

    /**
     * Get dateUpd
     *
     * @return \DateTime
     */
    public function getDateUpd()
    {
        return $this->date_upd;
    }
    /**
     * @var integer
     */
    private $ratio = 0;


    /**
     * Set ratio
     *
     * @param integer $ratio
     *
     * @return Torrent
     */
    public function setRatio($ratio)
    {
        $this->ratio = $ratio;

        return $this;
    }

    /**
     * Get ratio
     *
     * @return integer
     */
    public function getRatio()
    {
        return $this->ratio;
    }
    /**
     * @var float
     */
    private $percent = 0;


    /**
     * Set percent
     *
     * @param float $percent
     *
     * @return Torrent
     */
    public function setPercent($percent)
    {
        $this->percent = $percent;

        return $this;
    }

    /**
     * Get percent
     *
     * @return float
     */
    public function getPercent()
    {
        return $this->percent;
    }
    /**
     * @var \FrontBundle\Entity\Status
     */
    private $transfert;


    /**
     * Set transfert
     *
     * @param \FrontBundle\Entity\Status $transfert
     *
     * @return Torrent
     */
    public function setTransfert(\FrontBundle\Entity\Status $transfert = null)
    {
        $this->transfert = $transfert;

        return $this;
    }

    /**
     * Get transfert
     *
     * @return \FrontBundle\Entity\Status
     */
    public function getTransfert()
    {
        return $this->transfert;
    }

    /**
     * Set file
     *
     * @param string $file
     *
     * @return Torrent
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }
}
