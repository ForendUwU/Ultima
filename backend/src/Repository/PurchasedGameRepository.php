<?php

namespace App\Repository;

use App\Entity\PurchasedGame;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
}
