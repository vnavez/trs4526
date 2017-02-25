<?php

namespace FrontBundle\Service;

use FrontBundle\Repository\UserRepository;

class User
{
    /**
     * @var UserRepository UserRepository
     */
    private $userRepository;

    /**
     * User constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param $token
     * @return int|null
     */
    public function isAuthorizedToken($token) {
        /** @var \FrontBundle\Entity\User $user */
        $user = $this->userRepository->findOneBy(array('token' => $token));
        if (!$user)
            return null;

        return $user->getId();
    }

}