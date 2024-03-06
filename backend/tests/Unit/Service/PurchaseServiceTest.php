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
        $testUserWithMoney = new User();
        $testUserWithMoney->setLogin('testLogin');
        $testUserWithMoney->setBalance('999');

        $testUserWithoutMoney = new User();
        $testUserWithoutMoney->setLogin('testLogin');
        $testUserWithoutMoney->setBalance('0');

        $testGame = new Game();
        $testGame->setTitle('testTitle');
        $testGame->setPrice('99');

        $testPurchasedGame = new PurchasedGame();
        $testPurchasedGame->setUser($testUserWithMoney);
        $testPurchasedGame->setGame($testGame);

        return [
            'success' => [$testUserWithMoney, $testGame, null],
            'not enough money' => [$testUserWithoutMoney, $testGame, null],
            'game already purchased' => [$testUserWithMoney, $testGame, $testPurchasedGame]
        ];
    }

    /**
     *  @dataProvider purchaseDataProvider
     */
    public function testPurchase($testUser, $testGame, $testPurchasedGame)
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

        if (!$testPurchasedGame && $testUser->getBalance() === '999') {
            $result = $this->purchaseService->purchase(1, 1);

            $this->assertNotNull($result);
            $this->assertEquals('Successfully purchased', $result);
        } else if (!$testPurchasedGame && $testUser->getBalance() === '0') {
            $this->expectException(\Exception::class);
            $this->expectExceptionMessage('Not enough money');

            $this->purchaseService->purchase(1, 1);
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

        $this->getEntitiesServiceMock
            ->expects($this->once())
            ->method('getUserByLogin')
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