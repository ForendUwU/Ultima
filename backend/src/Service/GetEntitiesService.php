<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\PurchasedGame;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class GetEntitiesService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {

    }

    /**
     * @throws \Exception
     */
    public function getUserByLogin($login): User
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['login' => $login]);

        if (!$user) {
            throw new \Exception('User not found', Response::HTTP_NOT_FOUND);
        }

        return $user;
    }

    /**
     * @throws \Exception
     */
    public function getGameById($gameId): Game
    {
        $game = $this->em->getRepository(Game::class)->findOneBy(['id' => $gameId]);

        if (!$game) {
            throw new \Exception('Game not found', Response::HTTP_NOT_FOUND);
        }

        return $game;
    }

    /**
     * @throws \Exception
     */
    public function getPurchasedGameByGameAndUser($game, $user): PurchasedGame
    {
        $purchasedGame = $this->em->getRepository(PurchasedGame::class)->findOneBy(['user' => $user, 'game' => $game]);

        if (!$purchasedGame) {
            throw new \Exception('You do not have this game', Response::HTTP_FORBIDDEN);
        }

        return $purchasedGame;
    }
}