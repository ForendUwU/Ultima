<?php

namespace App\Tests\Unit\Service;

use App\Entity\Game;
use App\Entity\PurchasedGame;
use App\Entity\User;
use App\Repository\GamesRepository;
use App\Repository\PurchasedGameRepository;
use App\Repository\UserRepository;
use App\Service\AuthorizationService;
use App\Service\GetEntitiesService;
use App\Service\PurchaseService;
use App\Service\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Response;

class PurchaseServiceTest extends TestCase
{
    private $emMock;
    private $getEntitiesServiceMock;
    private PurchaseService $purchaseService;

    public function setUp(): void
    {
        $this->emMock = $this->createMock(EntityManagerInterface::class);
        $this->getEntitiesServiceMock = $this->createMock(GetEntitiesService::class);
        $this->purchaseService = new PurchaseService($this->emMock, $this->getEntitiesServiceMock);
    }

    public function purchaseDataProvider(): array
    {
        $testUser = new User();
        $testUser->setLogin('testLogin');

        $testGame = new Game();
        $testGame->setTitle('testTitle');

        $testPurchasedGame = new PurchasedGame();
        $testPurchasedGame->setUser($testUser);
        $testPurchasedGame->setGame($testGame);

        return [
            'success' => [$testUser, $testGame, null],
            'game already purchased' => [$testUser, $testGame, $testPurchasedGame]
        ];
    }

    /**
     *  @dataProvider purchaseDataProvider
     */
    public function testPurchase1($testUser, $testGame, $testPurchasedGame)
    {
        $purchasedGameRepositoryMock = $this->createMock(PurchasedGameRepository::class);

        $this->getEntitiesServiceMock
            ->expects($this->once())
            ->method('getUserByLogin')
            ->willReturn($testUser);
        $this->getEntitiesServiceMock
            ->expects($this->once())
            ->method('getGameById')
            ->willReturn($testGame);

        $this->emMock
            ->expects($this->any())
            ->method('getRepository')
            ->willReturn($purchasedGameRepositoryMock);
        $purchasedGameRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($testPurchasedGame);

        if (!$testPurchasedGame) {
            $result = $this->purchaseService->purchase(1, 1);

            $this->assertNotNull($result);
            $this->assertEquals('Successfully purchased', $result);
        } else {
            $this->expectException(\Exception::class);
            $this->expectExceptionMessage('Game already purchased');

            $this->purchaseService->purchase(1, 1);
        }
    }

    public function testGetPurchasedGamesSuccess()
    {
        $testUser = new User();
        $testUser->setLogin('testLogin');
        $testUser->setToken('someToken');

        $testGame = new Game();
        $testGame->setTitle('testTitle');
        $testGame2 = new Game();
        $testGame2->setTitle('testTitle2');

        $testPurchasedGame = new PurchasedGame();
        $testPurchasedGame->setGame($testGame);
        $testPurchasedGame->setUser($testUser);

        $testPurchasedGame2 = new PurchasedGame();
        $testPurchasedGame2->setGame($testGame2);
        $testPurchasedGame2->setUser($testUser);

        $testUser->addPurchasedGame($testPurchasedGame);
        $testUser->addPurchasedGame($testPurchasedGame2);

        $userRepositoryMock = $this->createMock(UserRepository::class);

        $this->emMock
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($userRepositoryMock);
        $userRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($testUser);

        $result = $this->purchaseService->getPurchasedGames('someToken');

        $this->assertNotEmpty($result);
        $this->assertArrayHasKey(0, $result);
        $this->assertArrayHasKey(1, $result);
        $this->assertNotEmpty($result[0]);
        $this->assertArrayHasKey('gameId', $result[0]);
        $this->assertArrayHasKey('title', $result[0]);
        $this->assertArrayHasKey('hoursOfPlaying', $result[0]);
        $this->assertEquals($testGame->getId(), $result[0]['gameId']);
        $this->assertEquals($testGame->getTitle(), $result[0]['title']);
        $this->assertEquals(0, $result[0]['hoursOfPlaying']);
    }
}