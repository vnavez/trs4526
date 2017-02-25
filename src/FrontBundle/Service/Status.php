<?php

namespace FrontBundle\Service;

use FrontBundle\Repository\StatusRepository;

class Status
{
    /**
     * @var StatusRepository StatusRepository
     */
    private $statusRepository;

    /**
     * @var array $status
     */
    private $status;

    /**
     * Status constructor.
     * @param StatusRepository $statusRepository
     * @param array $status
     */
    public function __construct(StatusRepository $statusRepository, $status)
    {
        $this->statusRepository = $statusRepository;
        $this->status = $status;
    }

    /**
     * @param $code
     * @return int|\FrontBundle\Entity\Status
     */
    public function getStatusByCode($code) {
        /** @var \FrontBundle\Entity\Status $status */
        $status = $this->statusRepository->findOneBy(array('code' => $this->status[$code]));
        if ($status)
            return $status;
        return null;
    }

}