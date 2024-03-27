<?php

namespace App\Handler;

use App\Entity\Review;
use App\Message\Rating;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RatingHandler
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {

    }

    public function __invoke(Rating $rating): void
    {
        $review = $this->em->getRepository(Review::class)->findById($rating->getReviewId());

        $game = $review->getGame();

        if ($rating->getRating()) {
            if ($review->getRating()) {
                if ($review->getRating() === 1 && $rating->getRating() === 2) {
                    $game->decreaseLikes();
                    $game->increaseDislikes();
                }
                if ($review->getRating() === 2 && $rating->getRating() === 1) {
                    $game->increaseLikes();
                    $game->decreaseDislikes();
                }
            } else {
                $rating->getRating() === 1 ? $game->increaseLikes() : $game->increaseDislikes();
            }

            $review->setRating($rating->getRating());
        } else {
            $review->getRating() === 1 ? $game->decreaseLikes() : $game->decreaseDislikes();
        }
        $this->em->flush();
    }
}
