<?php

namespace FrontBundle\Entity;

/**
 * Transfer
 */
class Transfer
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \FrontBundle\Entity\User
     */
    private $user;

    /**
     * @var \FrontBundle\Entity\Torrent
     */
    private $torrent;

    /**
     * @var \FrontBundle\Entity\Status
     */
    private $transfert;


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
     * Set user
     *
     * @param \FrontBundle\Entity\User $user
     *
     * @return Transfer
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
     * Set torrent
     *
     * @param \FrontBundle\Entity\Torrent $torrent
     *
     * @return Transfer
     */
    public function setTorrent(\FrontBundle\Entity\Torrent $torrent = null)
    {
        $this->torrent = $torrent;

        return $this;
    }

    /**
     * Get torrent
     *
     * @return \FrontBundle\Entity\Torrent
     */
    public function getTorrent()
    {
        return $this->torrent;
    }

    /**
     * Set transfert
     *
     * @param \FrontBundle\Entity\Status $transfert
     *
     * @return Transfer
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
     * @var \FrontBundle\Entity\Status
     */
    private $status;


    /**
     * Set status
     *
     * @param \FrontBundle\Entity\Status $status
     *
     * @return Transfer
     */
    public function setStatus(\FrontBundle\Entity\Status $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return \FrontBundle\Entity\Status
     */
    public function getStatus()
    {
        return $this->status;
    }
}
