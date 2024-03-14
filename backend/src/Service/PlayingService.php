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
    public function savePlayingTime($gameId, $login, $time): void
    {
        $user = $this->em->getRepository(User::class)->findByLogin($login);
        $game = $this->em->getRepository(Game::class)->findById($gameId);

        $purchasedGame = $this->em->getRepository(PurchasedGame::class)->findByGameAndUser($game, $user);

        $purchasedGame->setHoursOfPlaying($time / self::HOUR_IN_MILLISECONDS);
        $this->em->flush();
    }
}
