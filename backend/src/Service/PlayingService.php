<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class PlayingService
{
    public function __construct(
        private readonly GetEntitiesService $getEntitiesService,
        private readonly EntityManagerInterface $em
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

        $purchasedGame->setHoursOfPlaying($time/3600000);
        $this->em->flush();
    }
}
