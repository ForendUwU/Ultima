<?php

namespace App\Service;

use App\Entity\PurchasedGame;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserInfoService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly GetEntitiesService $getEntitiesService,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
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

    /**
     * @throws \Exception
     */
    public function getUsersMostPlayedGames($userLogin): array
    {
        $user = $this->getEntitiesService->getUserByLogin($userLogin);

        $purchasedGames = $this->em->getRepository(PurchasedGame::class)->findBy(
            ['user' => $user],
            ['hoursOfPlaying' => 'DESC'],
            5
        );

        return array_map(function($item){
            return [
                'title' => $item->getGame()->getTitle(),
                'hoursOfPlaying' => $item->getHoursOfPlaying()
            ];
        }, $purchasedGames);
    }

    public function validatePassword($userLogin, $password): bool
    {
        $user = $this->getEntitiesService->getUserByLogin($userLogin);
        return $this->userPasswordHasher->isPasswordValid($user, $password);
    }

    public function updateUserInfo($userLogin, $data): void
    {
        $user = $this->getEntitiesService->getUserByLogin($userLogin);
        if ($data['nickname']) {
            $user->setNickname($data['nickname']);
        }
        if ($data['password']) {
            $user->setPassword($data['password']);
        }
        if ($data['firstName']) {
            $user->setFirstName($data['firstName']);
        } else {
            $user->setFirstName('');
        }
        if ($data['lastName']) {
            $user->setLastName($data['lastName']);
        } else {
            $user->setLastName('');
        }
        if ($data['email']) {
            $user->setEmail($data['email']);
        }
        $this->em->persist($user);
        $this->em->flush();
    }
}
