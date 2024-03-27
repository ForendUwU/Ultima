<?php

namespace App\Message;

class Rating
{
    public function __construct(
        private ?int $rating,
        private int $reviewId
    ) {
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function getReviewId(): int
    {
        return $this->reviewId;
    }
}
