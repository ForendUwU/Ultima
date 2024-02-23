<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserInfoService
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {

    }

    public function getUserInfo($userLogin): array
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['login' => $userLogin]);
        return [
            'login' => $user->getLogin(),
            'nickname' => $user->getNickname(),
            'balance' => $user->getBalance(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail(),
            'purchasedGames' => $user->getPurchasedGames()
        ];
    }
}
