<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class AccountFundingService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private GetEntitiesService $getEntitiesService
    ) {

    }

    /**
     * @throws \Exception
     */
    public function fund($amount, $login): string
    {
        $user = $this->getEntitiesService->getUserByLogin($login);
        $user->setBalance(bcadd($user->getBalance(), $amount, 2));
        $this->em->flush();

        return 'successfully funded';
    }
}
