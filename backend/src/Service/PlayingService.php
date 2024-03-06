<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class PlayingService
{
    private const HOUR_IN_MILLISECONDS = 3600000;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly GetEntitiesService $getEntitiesService
    ) {

    }

    /**
     * @throws \Exception
     */
    public function savePlayingTime($gameId, $login, $time): void
    {
        $user = $this->getEntitiesService->getUserByLogin($login);
        $game = $this->getEntitiesService->getGameById($gameId);

        $purchasedGame = $this->getEntitiesService->getPurchasedGameByGameAndUser($game, $user);

        $purchasedGame->setHoursOfPlaying($time / self::HOUR_IN_MILLISECONDS);
        $this->em->flush();
    }
}
