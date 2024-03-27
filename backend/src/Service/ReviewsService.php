<?php

namespace App\Service;

use App\Entity\Game;
use App\Entity\Review;
use App\Entity\User;
use App\Message\Rating;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Messenger\MessageBusInterface;

class ReviewsService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private MessageBusInterface $bus
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

        if ($reviewContent !== '') {
            $review->setContent($reviewContent);
            $review->setFull(true);
        } else {
            $review->setContent('');
            $review->setFull(false);
        }

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
                'userNickname' => $item->getUser()->getNickname(),
                'isFull' => $item->isFull()
            ];
        }, $this->em->getRepository(Review::class)->findBy(['game' => $game]));
    }

    /**
     * @throws \Exception
     */
    public function changeGameReviewContent($reviewContent, $reviewId): Review
    {
        $review = $this->em->getRepository(Review::class)->findById($reviewId);

        if ($reviewContent != '') {
            $review->setContent($reviewContent);
            $review->setFull(true);
        }

        $this->em->flush();

        return $review;
    }

    /**
     * @throws \Exception
     */
    public function deleteUsersReview($reviewId): void
    {
        $review = $this->em->getRepository(Review::class)->findById($reviewId);
        $rating = new Rating(null, $reviewId);
        $this->bus->dispatch($rating);
        $this->em->remove($review);
        sleep(1);
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
            'content' => $review->getContent(),
            'rating' => $review->getRating()
        ] : null;
    }

    public function sendRating(int $reviewId, ?int $rating): void
    {
        $review = $this->em->getRepository(Review::class)->findById($reviewId);

        if ($review->getRating() === $rating) {
            throw new \Exception('Already has this rate', Response::HTTP_FORBIDDEN);
        }

        $this->bus->dispatch(new Rating($rating, $reviewId));
        sleep(1);
    }
}
