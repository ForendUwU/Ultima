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

//    /**
//     * @return PurchasedGame[] Returns an array of PurchasedGame objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PurchasedGame
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
