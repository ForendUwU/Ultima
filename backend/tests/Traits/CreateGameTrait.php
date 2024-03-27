<?php

namespace App\Tests\Traits;

use App\Entity\Game;

trait CreateGameTrait
{
    public static function createGame(
        $title = 'testTitle',
        $price = 0,
        $description = 'testDescription',
        $likes = 0,
        $dislikes = 0
    ): Game {
        $testGame = new Game();
        $testGame->setTitle($title);
        $testGame->setPrice($price);
        $testGame->setDescription($description);
        $testGame->setLikes($likes);
        $testGame->setDislikes($dislikes);

        return $testGame;
    }

    public static function setGameRepositoryAsReturnFromEntityManager($gameRepositoryMock): void
    {
        static::$emMock
            ->expects(static::once())
            ->method('getRepository')
            ->willReturn($gameRepositoryMock);
    }

    public static function setTestGameAsReturnFromRepositoryMock($gameRepositoryMock, $testGame): void
    {
        $gameRepositoryMock
            ->expects(static::once())
            ->method('findById')
            ->willReturn($testGame);
    }
}