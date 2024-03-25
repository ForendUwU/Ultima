<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\PurchasedGame;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class PlayingService
{
    private const HOUR_IN_MILLISECONDS = 3600000;

    public function __construct(
        private readonly EntityManagerInterface $em
    ) {

    }

    /**
     * @throws \Exception
     */
    public function savePlayingTime($purchasedGameId, $time): void
    {
        $purchasedGame = $this->em->getRepository(PurchasedGame::class)->findById($purchasedGameId);

        $purchasedGame->setHoursOfPlaying($time / self::HOUR_IN_MILLISECONDS);
        $this->em->flush();
    }
}
