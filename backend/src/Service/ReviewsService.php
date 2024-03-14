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
    public function createGameReview($reviewContent, $userLogin, $gameId): Review
    {
        $user = $this->em->getRepository(User::class)->findByLogin($userLogin);
        $game = $this->em->getRepository(Game::class)->findById($gameId);

        $checkIfReviewExists = $this->em->getRepository(Review::class)->findOneBy(['user' => $user, 'game' => $game]);
        if ($checkIfReviewExists)
            throw new \Exception('User already has review on this game', Response::HTTP_UNPROCESSABLE_ENTITY);

        $review = new Review();
        $review->setContent($reviewContent);
        $review->setUser($user);
        $review->setGame($game);
        $review->setDislikes(0);
        $review->setLikes(0);

        $this->em->persist($review);
        $this->em->flush();

        return $review;
    }

    public function getGameReviews($gameId): array
    {
        $game = $this->em->getRepository(Game::class)->findById($gameId);

        return array_map(function ($item) {
            return [
                'content' => $item->getContent(),
                'likes' => $item->getLikes(),
                'dislikes' => $item->getDislikes(),
                'userNickname' => $item->getUser()->getNickname(),
            ];
        }, $this->em->getRepository(Review::class)->findBy(['game' => $game]));
    }

    /**
     * @throws \Exception
     */
    public function changeGameReviewContent($reviewContent, $userLogin, $gameId): Review
    {
        $user = $this->em->getRepository(User::class)->findByLogin($userLogin);
        $game = $this->em->getRepository(Game::class)->findById($gameId);

        $review = $this->em->getRepository(Review::class)->findByGameAndUser($game, $user);
        if ($review) {
            $review->setContent($reviewContent);
        } else {
            throw new \Exception('User\'s review not found', Response::HTTP_NOT_FOUND);
        }

        $this->em->flush();

        return $review;
    }

    /**
     * @throws \Exception
     */
    public function deleteUsersReview($userLogin, $gameId): void
    {
        $user = $this->em->getRepository(User::class)->findByLogin($userLogin);
        $game = $this->em->getRepository(Game::class)->findById($gameId);

        $review = $this->em->getRepository(Review::class)->findByGameAndUser($game, $user);
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
        $user = $this->em->getRepository(User::class)->findByLogin($userLogin);
        $game = $this->em->getRepository(Game::class)->findById($gameId);

        $review = $this->em->getRepository(Review::class)->findOneBy(['user' => $user, 'game' => $game]);

        return $review ? $review->getContent() : null;
    }
}