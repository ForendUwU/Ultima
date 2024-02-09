<?php

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\EntityManagerInterface;

class UserInfoService
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {

    }

    public function getUserInfo($userId): array
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => $userId]);
        return array(
            'content' => [
                'login' => $user->getLogin(),
                'nickname' => $user->getNickname(),
                'balance' => $user->getBalance(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'email' => $user->getEmail(),
                'purchasedGames' => $user->getPurchasedGames()
            ]
        );
    }
}
