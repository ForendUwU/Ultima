<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class AccountFundingService
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {

    }

    /**
     * @throws \Exception
     */
    public function fund($amount, $login): ?string
    {
        $user = $this->em->getRepository(User::class)->findByLogin($login);
        $user->setBalance(bcadd($user->getBalance(), $amount, 2));
        $this->em->flush();
        return $user->getBalance();
    }
}
