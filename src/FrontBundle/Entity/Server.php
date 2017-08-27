<?php

namespace FrontBundle\Entity;

/**
 * Server
 */
class Server
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $host;

    /**
     * @var integer
     */
    private $port;

    /**
     * @var string
     */
    private $username;

    /**
     * @var \FrontBundle\Entity\User
     */
    private $user;


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
     * Set host
     *
     * @param string $host
     *
     * @return Server
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get host
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set port
     *
     * @param integer $port
     *
     * @return Server
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get port
     *
     * @return integer
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return Server
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set user
     *
     * @param \FrontBundle\Entity\User $user
     *
     * @return Server
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
     * @var string
     */
    private $default_directory;


    /**
     * Set defaultDirectory
     *
     * @param string $defaultDirectory
     *
     * @return Server
     */
    public function setDefaultDirectory($defaultDirectory)
    {
        $this->default_directory = $defaultDirectory;

        return $this;
    }

    /**
     * Get defaultDirectory
     *
     * @return string
     */
    public function getDefaultDirectory()
    {
        return $this->default_directory;
    }
    /**
     * @var boolean
     */
    private $percent = 0;


    /**
     * Set percent
     *
     * @param boolean $percent
     *
     * @return Server
     */
    public function setPercent($percent)
    {
        $this->percent = $percent;

        return $this;
    }

    /**
     * Get percent
     *
     * @return boolean
     */
    public function getPercent()
    {
        return $this->percent;
    }
    /**
     * @var boolean
     */
    private $active = 0;


    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Server
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }
}
