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
        private readonly EntityManagerInterface $em,
        private readonly GetEntitiesService $getEntitiesService
    ) {

    }

    /**
     * @throws \Exception
     */
    public function purchase($gameId, $userLogin): string
    {
        $user = $this->getEntitiesService->getUserByLogin($userLogin);
        $game = $this->getEntitiesService->getGameById($gameId);

        $purchasedGames = $this->em->getRepository(PurchasedGame::class)->findOneBy(['user' => $user, 'game' => $game]);

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

            $this->em->persist($purchasedGame);
            $this->em->flush();

            return 'Successfully purchased';
        }
    }

    public function getPurchasedGames($login): array
    {
        $user = $this->getEntitiesService->getUserByLogin($login);

        $result = $user->getPurchasedGames()->map(static fn ($purchasedGame) => [
            'gameId' => $purchasedGame->getGame()->getId(),
            'title' => $purchasedGame->getGame()->getTitle(),
            'hoursOfPlaying' =>  $purchasedGame->getHoursOfPlaying()
        ]);

        return $result->getValues();
    }

    /**
     * @throws \Exception
     */
    public function deletePurchasedGame($gameId, $login): void
    {
        $user = $this->getEntitiesService->getUserByLogin($login);
        $game = $this->getEntitiesService->getGameById($gameId);

        $purchasedGame = $this->getEntitiesService->getPurchasedGameByGameAndUser($game, $user);

        $user->removePurchasedGame($purchasedGame);
        $this->em->remove($purchasedGame);

        $this->em->flush();
    }
}

