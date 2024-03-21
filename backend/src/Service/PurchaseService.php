<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\PurchasedGame;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class PurchaseService
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {

    }

    /**
     * @throws \Exception
     */
    public function purchase($gameId, $userId): string
    {
        $user = $this->em->getRepository(User::class)->findById($userId);
        $game = $this->em->getRepository(Game::class)->findById($gameId);

        $purchasedGames = $this->em->getRepository(PurchasedGame::class)->findByGameAndUser($game, $user);

        if ($purchasedGames) {
            throw new \Exception('Game already purchased', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $userBalanceAfterPurchasing = bcsub($user->getBalance(), $game->getPrice(), 2);

        if ($userBalanceAfterPurchasing < 0) {
            throw new \Exception('Not enough money', Response::HTTP_FORBIDDEN);
        } else {
            $user->setBalance($userBalanceAfterPurchasing);

            $purchasedGame = new PurchasedGame();
            $purchasedGame->setUser($user);
            $purchasedGame->setGame($game);

            $user->addPurchasedGame($purchasedGame);
            $game->addPurchasedGame($purchasedGame);

            $this->em->persist($purchasedGame);
            $this->em->flush();

            return 'Successfully purchased';
        }
    }

    public function getPurchasedGames($userId): array
    {
        $user = $this->em->getRepository(User::class)->findById($userId);

        $result = $user->getPurchasedGames()->map(fn ($purchasedGame) => [
            'id' => $purchasedGame->getId(),
            'gameId' => $purchasedGame->getGame()->getId(),
            'title' => $purchasedGame->getGame()->getTitle(),
            'hoursOfPlaying' =>  $purchasedGame->getHoursOfPlaying()
        ]);

        return $result->getValues();
    }

    /**
     * @throws \Exception
     */
    public function deletePurchasedGame($purchasedGameId): void
    {
        $purchasedGame = $this->em->getRepository(PurchasedGame::class)->findById($purchasedGameId);

        $this->em->remove($purchasedGame);

        $this->em->flush();
    }
}

