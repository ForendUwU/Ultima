<?php

namespace App\Tests\Unit\Service;

use App\Entity\Game;
use App\Entity\PurchasedGame;
use App\Entity\User;
use App\Service\GetEntitiesService;
use App\Service\PlayingService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class PlayingServiceTest extends TestCase
{
    private $emMock;
    private $getEntitiesServiceMock;
    private PlayingService $playingService;
    public function setUp(): void
    {
        $this->emMock = $this->createMock(EntityManagerInterface::class);
        $this->getEntitiesServiceMock = $this->createMock(GetEntitiesService::class);
        $this->playingService = new PlayingService(
            $this->emMock,
            $this->getEntitiesServiceMock
        );
    }

    public function testSavePlayingTime()
    {
        $testGame = new Game();

        $testUser = new User();
        $testUser->setLogin('testLogin');

        $testPurchasedGame = new PurchasedGame();
        $testPurchasedGame->setGame($testGame);
        $testPurchasedGame->setUser($testUser);

        $this->getEntitiesServiceMock
            ->expects($this->once())
            ->method('getGameById')
            ->willReturn($testGame);
        $this->getEntitiesServiceMock
            ->expects($this->once())
            ->method('getUserByLogin')
            ->willReturn($testUser);
        $this->getEntitiesServiceMock
            ->expects($this->once())
            ->method('getPurchasedGameByGameAndUser')
            ->willReturn($testPurchasedGame);

        $this->playingService->savePlayingTime($testGame->getId(), $testUser->getLogin(), 60000);

        $this->assertEquals(60000 / 3600000, $testPurchasedGame->getHoursOfPlaying());
    }
}