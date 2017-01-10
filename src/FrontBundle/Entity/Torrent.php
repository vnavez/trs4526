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
}

