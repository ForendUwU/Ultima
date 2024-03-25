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
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly AuthorizationService $authorizationService,
    ) {

    }

    public function getUserInfo($userId): array
    {
        $user = $this->em->getRepository(User::class)->findById($userId);
        return [
            'id' => $user->getId(),
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
    public function getUsersMostPlayedGames($userId): array
    {
        $user = $this->em->getRepository(User::class)->findById($userId);

        $purchasedGames = $this->em->getRepository(PurchasedGame::class)->findBy(
            ['user' => $user],
            ['hoursOfPlaying' => 'DESC'],
            5
        );

        return array_map(function($item) {
            return [
                'title' => $item->getGame()->getTitle(),
                'hoursOfPlaying' => $item->getHoursOfPlaying()
            ];
        }, $purchasedGames);
    }

    public function updateUserInfo($userId, $data): User
    {
        $user = $this->em->getRepository(User::class)->findById($userId);

        if ($data['nickname']) {
            $user->setNickname($data['nickname']);
        }
        if (isset($data['firstName'])) {
            $user->setFirstName($data['firstName']);
        } else {
            $user->setFirstName('');
        }
        if (isset($data['lastName'])) {
            $user->setLastName($data['lastName']);
        } else {
            $user->setLastName('');
        }
        if ($data['email']) {
            $user->setEmail($data['email']);
        }

        $this->em->flush();

        return $user;
    }

    /**
     * @throws \Exception
     */
    public function updatePassword($userId, $oldPassword, $newPassword): User
    {
        $user = $this->em->getRepository(User::class)->findById($userId);

        if ($this->userPasswordHasher->isPasswordValid($user, $oldPassword)) {
            $this->authorizationService->validatePassword($newPassword);

            $user->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    $newPassword
                )
            );

            $this->em->flush();

            return $user;
        } else {
            throw new \Exception('Old password incorrect');
        }
    }
}
