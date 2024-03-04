<?php

namespace App\Tests\Unit\Service;

use App\Entity\Game;
use App\Entity\PurchasedGame;
use App\Entity\User;
use App\Repository\GamesRepository;
use App\Repository\PurchasedGameRepository;
use App\Repository\UserRepository;
use App\Service\GetEntitiesService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class GetEntitiesServiceTest extends TestCase
{
    private $emMock;
    private getEntitiesService $getEntitiesService;

    public function setUp(): void
    {
        $this->emMock = $this->createMock(EntityManagerInterface::class);
        $this->getEntitiesService = new GetEntitiesService(
            $this->emMock
        );
    }

    public function getUserByTokenDataProvider()
    {
        $testUser = new User();
        $testUser->setLogin('testLogin');

        return [
            'success' => [$testUser],
            'user not found' => [null]
        ];
    }

    public function getGameByIdDataProvider()
    {
        $testGame = new Game();
        $testGame->setTitle('testTitle');

        return [
            'success' => [$testGame],
            'game not found' => [null]
        ];
    }

    public function getPurchasedGameDataProvider()
    {
        $testGame = new PurchasedGame();

        return [
            'success' => [$testGame],
            'game not found' => [null]
        ];
    }

    /**
     *  @dataProvider getUserByTokenDataProvider
     */
    public function testGetUserByLogin($testUser)
    {
        $userRepositoryMock = $this->createMock(UserRepository::class);

        $this->emMock
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($userRepositoryMock);
        $userRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($testUser);

        if ($testUser) {
            $result = $this->getEntitiesService->getUserByLogin('testLogin');
            $this->assertEquals($testUser, $result);
            $this->assertNotNull($testUser->getLogin());
            $this->assertEquals('testLogin', $testUser->getLogin());
        } else {
            $this->expectException(\Exception::class);
            $this->expectExceptionMessage('User not found');

            $this->getEntitiesService->getUserByLogin('testLogin');
        }
    }

    /**
     *  @dataProvider getGameByIdDataProvider
     */
    public function testGetGameById($testGame)
    {
        $gamesRepositoryMock = $this->createMock(GamesRepository::class);

        $this->emMock
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($gamesRepositoryMock);
        $gamesRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($testGame);

        if ($testGame) {
            $result = $this->getEntitiesService->getGameById(0);
            $this->assertEquals($testGame, $result);
        } else {
            $this->expectException(\Exception::class);
            $this->expectExceptionMessage('Game not found');

            $this->getEntitiesService->getGameById(0);
        }
    }

    /**
     *  @dataProvider getPurchasedGameDataProvider
     */
    public function testGetPurchasedGameByGameAndUser($testPurchasedGame)
    {
        $testUser = new User();
        $testUser->setLogin('testLogin');

        $testGame = new Game();
        $testGame->setTitle('testTitle');

        $purchasedGameRepositoryMock = $this->createMock(PurchasedGameRepository::class);

        $this->emMock
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($purchasedGameRepositoryMock);
        $purchasedGameRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($testPurchasedGame);

        if ($testPurchasedGame) {
            $result = $this->getEntitiesService->getPurchasedGameByGameAndUser($testGame, $testUser);
            $this->assertEquals($testPurchasedGame, $result);
        } else {
            $this->expectException(\Exception::class);
            $this->expectExceptionMessage('You do not have this game');

            $this->getEntitiesService->getPurchasedGameByGameAndUser($testGame, $testUser);
        }
    }
}