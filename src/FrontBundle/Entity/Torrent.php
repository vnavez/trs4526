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
}
