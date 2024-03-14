<?php

namespace App\Tests\Traits;

use App\Entity\Review;

trait CreateReviewTrait
{
    use CreateUserTrait, CreateGameTrait;

    public static function createReview(
        $user,
        $game,
        $likes = 0,
        $dislikes = 0,
        $content = 'testContent',
    ): Review {
        $testReview = new Review();
        $testReview->setUser($user ?: self::createUser());
        $testReview->setGame($game ?: self::createGame());
        $testReview->setLikes($likes);
        $testReview->setDislikes($dislikes);
        $testReview->setContent($content);

        return $testReview;
    }

    public static function setReviewRepositoryAsReturnFromEntityManager($reviewRepositoryMock): void
    {
        static::$emMock
            ->expects(static::once())
            ->method('getRepository')
            ->willReturn($reviewRepositoryMock);
    }

    public static function setTestReviewAsReturnFromRepositoryMock($reviewRepositoryMock, $testReview): void
    {
        $reviewRepositoryMock
            ->expects(static::once())
            ->method('findByGameAndUser')
            ->willReturn($testReview);
    }
}