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

        $purchasedGameRepositoryMock = $this->createMock(PurchasedGameRepository::class);

        static::$emMock
            ->expects(static::once())
            ->method('getRepository')
            ->willReturnOnConsecutiveCalls($purchasedGameRepositoryMock);


        $testPurchasedGame = $this->createPurchasedGame($testUser, $testGame);
        $this->setTestPurchasedGameAsReturnFromRepositoryMockById($purchasedGameRepositoryMock, $testPurchasedGame);

        $this->playingService->savePlayingTime($testGame->getId(), 60000);

        $this->assertEquals(60000 / 3600000, $testPurchasedGame->getHoursOfPlaying());
    }
}