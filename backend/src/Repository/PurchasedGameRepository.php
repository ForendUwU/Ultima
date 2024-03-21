<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\PurchasedGame;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

/**
 * @extends ServiceEntityRepository<PurchasedGame>
 *
 * @method PurchasedGame|null find($id, $lockMode = null, $lockVersion = null)
 * @method PurchasedGame|null findOneBy(array $criteria, array $orderBy = null)
 * @method PurchasedGame[]    findAll()
 * @method PurchasedGame[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PurchasedGameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PurchasedGame::class);
    }

    /**
     * @throws \Exception
     */
    public function findByGameAndUser(Game $game, User $user): PurchasedGame
    {
        $purchasedGame = $this->findOneBy(['user' => $user, 'game' => $game]);

        if (!$purchasedGame) {
            throw new \Exception('You do not have this game', Response::HTTP_FORBIDDEN);
        }

        return $purchasedGame;
    }

    /**
     * @throws \Exception
     */
    public function findById(int $id): PurchasedGame
    {
        $purchasedGame = $this->findOneBy(['id' => $id]);

        if (!$purchasedGame) {
            throw new \Exception('You do not have this game', Response::HTTP_FORBIDDEN);
        }

        return $purchasedGame;
    }
}
