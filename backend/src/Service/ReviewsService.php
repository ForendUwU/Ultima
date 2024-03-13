<?php

namespace App\Service;

use App\Entity\Review;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class ReviewsService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly GetEntitiesService $getEntitiesService
    ) {

    }

    public function getGameReviews($gameId): array
    {
        $game = $this->getEntitiesService->getGameById($gameId);

        return array_map(function ($item) {
            return [
                'content' => $item->getContent(),
                'likes' => $item->getLikes(),
                'dislikes' => $item->getDislikes(),
                'user' => $item->getUser()->getNickname(),
            ];
        }, $this->em->getRepository(Review::class)->findBy(['game' => $game]));
    }

    /**
     * @throws \Exception
     */
    public function createGameReview($reviewContent, $userLogin, $gameId): void
    {
        $user = $this->getEntitiesService->getUserByLogin($userLogin);
        $game = $this->getEntitiesService->getGameById($gameId);

        $checkIfReviewExists = $this->em->getRepository(Review::class)->findOneBy(['user' => $user, 'game' => $game]);
        if ($checkIfReviewExists)
            throw new \Exception('User already have review on this game', Response::HTTP_UNPROCESSABLE_ENTITY);

        $review = new Review();
        $review->setContent($reviewContent);
        $review->setUser($user);
        $review->setGame($game);
        $review->setDislikes(0);
        $review->setLikes(0);

        $this->em->persist($review);
        $this->em->flush();
    }

    /**
     * @throws \Exception
     */
    public function changeGameReviewContent($reviewContent, $userLogin, $gameId): void
    {
        $user = $this->getEntitiesService->getUserByLogin($userLogin);
        $game = $this->getEntitiesService->getGameById($gameId);

        $review = $this->em->getRepository(Review::class)->findOneBy(['user' => $user, 'game' => $game]);
        if ($review) {
            $review->setContent($reviewContent);
        } else {
            throw new \Exception('User\'s review not found', Response::HTTP_NOT_FOUND);
        }

        $this->em->flush();
    }

    /**
     * @throws \Exception
     */
    public function deleteUsersReviewContent($userLogin, $gameId): void
    {
        $user = $this->getEntitiesService->getUserByLogin($userLogin);
        $game = $this->getEntitiesService->getGameById($gameId);
        $review = $this->em->getRepository(Review::class)->findOneBy(['user' => $user, 'game' => $game]);
        if ($review) {
            $this->em->remove($review);
        } else {
            throw new \Exception('User\'s review not found', Response::HTTP_NOT_FOUND);
        }

        $this->em->flush();
    }

    /**
     * @throws \Exception
     */
    public function getUserReviewContentByUserLoginAndGameId($userLogin, $gameId): ?string
    {
        $user = $this->getEntitiesService->getUserByLogin($userLogin);
        $game = $this->getEntitiesService->getGameById($gameId);

        $review = $this->em->getRepository(Review::class)->findOneBy(['user' => $user, 'game' => $game]);

        return $review ? $review->getContent() : null;
    }
}