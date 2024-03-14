<?php

namespace App\Tests\Traits;

use App\Entity\PurchasedGame;

trait CreatePurchasedGameTrait
{
    use CreateGameTrait, CreateUserTrait;

    public static function createPurchasedGame(
        $user,
        $game,
        $hoursOfPlaying = 0,
    ): PurchasedGame {
        $testPurchasedGame = new PurchasedGame();
        $testPurchasedGame->setUser($user ?: self::createUser());
        $testPurchasedGame->setGame($game ?: self::createGame());
        $testPurchasedGame->setHoursOfPlaying($hoursOfPlaying);

        return $testPurchasedGame;
    }

    public static function setPurchasedGameRepositoryAsReturnFromEntityManager($purchasedGameRepositoryMock): void
    {
        static::$emMock
            ->expects(static::once())
            ->method('getRepository')
            ->willReturn($purchasedGameRepositoryMock);
    }

    public static function setTestPurchasedGameAsReturnFromRepositoryMock($purchasedGameRepositoryMock, $testPurchasedGame): void
    {
        $purchasedGameRepositoryMock
            ->expects(static::once())
            ->method('findByGameAndUser')
            ->willReturn($testPurchasedGame);
    }
}