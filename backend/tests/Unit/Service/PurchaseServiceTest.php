<?php

namespace App\Tests\Unit\Service;

use App\Repository\GamesRepository;
use App\Repository\PurchasedGameRepository;
use App\Repository\UserRepository;
use App\Service\PurchaseService;
use App\Tests\Traits\CreateGameTrait;
use App\Tests\Traits\CreatePurchasedGameTrait;
use App\Tests\Traits\CreateUserTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class PurchaseServiceTest extends TestCase
{
    use CreateUserTrait, CreateGameTrait, CreatePurchasedGameTrait;

    public static $emMock;
    private PurchaseService $purchaseService;

    public function setUp(): void
    {
        static::$emMock = $this->createMock(EntityManagerInterface::class);
        $this->purchaseService = new PurchaseService(
            static::$emMock
        );
    }

    public function purchaseDataProvider(): array
    {
        $testUserWithMoney = $this->createUser(balance: 999);

        $testUserWithoutMoney = $this->createUser(balance: 0);

        $testGame = $this->createGame(price: 99);

        $testPurchasedGame = $this->createPurchasedGame($testUserWithMoney, $testGame);

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
        $userRepositoryMock = $this->createMock(UserRepository::class);
        $gameRepositoryMock = $this->createMock(GamesRepository::class);
        $purchasedGameRepositoryMock = $this->createMock(PurchasedGameRepository::class);

        static::$emMock
            ->expects($this->exactly(3))
            ->method('getRepository')
            ->willReturnOnConsecutiveCalls($userRepositoryMock, $gameRepositoryMock, $purchasedGameRepositoryMock);

        $this->setTestUserAsReturnFromRepositoryMock($userRepositoryMock, $testUser);
        $this->setTestGameAsReturnFromRepositoryMock($gameRepositoryMock, $testGame);
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

    public function testGetPurchasedGames()
    {
        $testUser = $this->createUser();

        $testGame1 = $this->createGame(title: 'testTitle1');
        $testGame2 = $this->createGame(title: 'testTitle2');

        $testPurchasedGame1 = $this->createPurchasedGame($testUser, $testGame1);
        $testPurchasedGame2 = $this->createPurchasedGame($testUser, $testGame2);

        $testUser->addPurchasedGame($testPurchasedGame1);
        $testUser->addPurchasedGame($testPurchasedGame2);

        $userRepositoryMock = $this->createMock(UserRepository::class);

        $this->setUserRepositoryAsReturnFromEntityManager($userRepositoryMock);
        $this->setTestUserAsReturnFromRepositoryMock($userRepositoryMock, $testUser);

        $result = $this->purchaseService->getPurchasedGames($testUser->getLogin());

        $this->assertNotEmpty($result);
        $this->assertArrayHasKey(0, $result);
        $this->assertArrayHasKey(1, $result);
        $this->assertNotEmpty($result[0]);
        $this->assertArrayHasKey('gameId', $result[0]);
        $this->assertArrayHasKey('title', $result[0]);
        $this->assertArrayHasKey('hoursOfPlaying', $result[0]);
        $this->assertEquals($testGame1->getId(), $result[0]['gameId']);
        $this->assertEquals($testGame1->getTitle(), $result[0]['title']);
        $this->assertEquals(0, $result[0]['hoursOfPlaying']);
    }
}