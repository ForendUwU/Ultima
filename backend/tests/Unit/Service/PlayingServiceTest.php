<?php

namespace App\Tests\Unit\Service;

use App\Repository\GamesRepository;
use App\Repository\PurchasedGameRepository;
use App\Repository\UserRepository;
use App\Service\PlayingService;
use App\Tests\Traits\CreateGameTrait;
use App\Tests\Traits\CreatePurchasedGameTrait;
use App\Tests\Traits\CreateUserTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class PlayingServiceTest extends TestCase
{
    use CreateUserTrait, CreateGameTrait, CreatePurchasedGameTrait;

    public static $emMock;
    private PlayingService $playingService;
    public function setUp(): void
    {
        static::$emMock = $this->createMock(EntityManagerInterface::class);
        $this->playingService = new PlayingService(
            static::$emMock
        );
    }

    public function testSavePlayingTime()
    {
        $testUser = $this->createUser();
        $testGame = $this->createGame();

        $userRepositoryMock = $this->createMock(UserRepository::class);
        $gameRepositoryMock = $this->createMock(GamesRepository::class);
        $purchasedGameRepositoryMock = $this->createMock(PurchasedGameRepository::class);

        static::$emMock
            ->expects(static::exactly(3))
            ->method('getRepository')
            ->willReturnOnConsecutiveCalls($userRepositoryMock, $gameRepositoryMock, $purchasedGameRepositoryMock);

        $this->setTestUserAsReturnFromRepositoryMock($userRepositoryMock, $testUser);
        $this->setTestGameAsReturnFromRepositoryMock($gameRepositoryMock, $testGame);

        $testPurchasedGame = $this->createPurchasedGame($testUser, $testGame);
        $this->setTestPurchasedGameAsReturnFromRepositoryMock($purchasedGameRepositoryMock, $testPurchasedGame);

        $this->playingService->savePlayingTime($testGame->getId(), $testUser->getLogin(), 60000);

        $this->assertEquals(60000 / 3600000, $testPurchasedGame->getHoursOfPlaying());
    }
}