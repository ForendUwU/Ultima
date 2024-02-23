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
      private readonly TokenService $tokenService
    ) {

    }

    /**
     * @throws \Exception
     */
    public function purchase($gameId, $userLogin): string
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['login' => $userLogin]);
        $game = $this->em->getRepository(Game::class)->findOneBy(['id' => $gameId]);

        $purchasedGames = $this->em->getRepository(PurchasedGame::class)->findOneBy(['user' => $user, 'game' => $game]);

        if ($purchasedGames) {
            throw new \Exception('Game already purchased', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $purchasedGame = new PurchasedGame();
        $purchasedGame->setUser($user);
        $purchasedGame->setGame($game);

        $user->addPurchasedGame($purchasedGame);

        $this->em->persist($purchasedGame);
        $this->em->flush();

        return 'Successfully purchased';
    }

    public function getPurchasedGames($token): array
    {
        $decodedToken = $this->tokenService->decodeLongToken($token);
        $user = $this->em->getRepository(User::class)->findOneBy(['login' => $decodedToken->login]);

        $result = $user->getPurchasedGames()->map(static fn ($purchasedGame) => [
            'gameId' => $purchasedGame->getGame()->getId(),
            'title' => $purchasedGame->getGame()->getTitle(),
            'hoursOfPlaying' =>  $purchasedGame->getHoursOfPlaying()
        ]);

        return $result->getValues();
    }
}

