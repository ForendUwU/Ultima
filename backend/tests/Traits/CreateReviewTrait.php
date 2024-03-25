<?php

namespace App\Tests\Traits;

use App\Entity\Review;

trait CreateReviewTrait
{
    use CreateUserTrait, CreateGameTrait;

    public static function createReview(
        $user,
        $game,
        $content = 'testContent',
    ): Review {
        $testReview = new Review();
        $testReview->setUser($user ?: self::createUser());
        $testReview->setGame($game ?: self::createGame());
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

    public static function setTestReviewAsReturnFromRepositoryMockByGameAndUser($reviewRepositoryMock, $testReview): void
    {
        $reviewRepositoryMock
            ->expects(static::once())
            ->method('findByGameAndUser')
            ->willReturn($testReview);
    }

    public static function setTestReviewAsReturnFromRepositoryMockById($reviewRepositoryMock, $testReview): void
    {
        $reviewRepositoryMock
            ->expects(static::once())
            ->method('findById')
            ->willReturn($testReview);
    }
}