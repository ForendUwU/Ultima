<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\Review;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

/**
 * @extends ServiceEntityRepository<Review>
 *
 * @method Review|null find($id, $lockMode = null, $lockVersion = null)
 * @method Review|null findOneBy(array $criteria, array $orderBy = null)
 * @method Review[]    findAll()
 * @method Review[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    /**
     * @throws \Exception
     */
    public function findByGameAndUser(Game $game, User $user): Review
    {
        $review = $this->findOneBy(['user' => $user, 'game' => $game]);

        if (!$review){
            throw new \Exception('Review not found', Response::HTTP_NOT_FOUND);
        }

        return $review;
    }
}
