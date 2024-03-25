<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\Review;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class ReviewsService
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {

    }

    /**
     * @throws \Exception
     */
    public function createGameReview($reviewContent, $userId, $gameId): Review
    {
        $user = $this->em->getRepository(User::class)->findById($userId);
        $game = $this->em->getRepository(Game::class)->findById($gameId);

        $checkIfReviewExists = $this->em->getRepository(Review::class)->findOneBy(['user' => $user, 'game' => $game]);
        if ($checkIfReviewExists) {
            throw new \Exception('User already has review on this game', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $review = new Review();
        $review->setContent($reviewContent);
        $review->setUser($user);
        $review->setGame($game);

        $this->em->persist($review);
        $this->em->flush();

        return $review;
    }

    public function getGameReviews($gameId): array
    {
        $game = $this->em->getRepository(Game::class)->findById($gameId);

        return array_map(function ($item) {
            return [
                'id' => $item->getId(),
                'content' => $item->getContent(),
                'userNickname' => $item->getUser()->getNickname()
            ];
        }, $this->em->getRepository(Review::class)->findBy(['game' => $game]));
    }

    /**
     * @throws \Exception
     */
    public function changeGameReviewContent($reviewContent, $reviewId): Review
    {
        $review = $this->em->getRepository(Review::class)->findById($reviewId);

        $review->setContent($reviewContent);
        $this->em->flush();

        return $review;
    }

    /**
     * @throws \Exception
     */
    public function deleteUsersReview($reviewId): void
    {
        $review = $this->em->getRepository(Review::class)->findById($reviewId);

        $this->em->remove($review);
        $this->em->flush();
    }

    /**
     * @throws \Exception
     */
    public function getUserReview($userId, $gameId): ?array
    {
        $user = $this->em->getRepository(User::class)->findById($userId);
        $game = $this->em->getRepository(Game::class)->findById($gameId);

        $review = $this->em->getRepository(Review::class)->findOneBy(['user' => $user, 'game' => $game]);

        return $review ? [
            'id' => $review->getId(),
            'content' => $review->getContent()
        ] : null;
    }
}
