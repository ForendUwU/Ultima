<?php

namespace App\Tests\Unit\Service;

use App\Entity\Game;
use App\Entity\PurchasedGame;
use App\Entity\User;
use App\Repository\GamesRepository;
use App\Repository\PurchasedGameRepository;
use App\Repository\UserRepository;
use App\Service\AuthorizationService;
use App\Service\PurchaseService;
use App\Service\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Response;

class PurchaseServiceTest extends TestCase
{
    private $tokenServiceMock;
    private $emMock;
    private PurchaseService $purchaseService;

    public function createService(): void
    {
        $this->emMock = $this->createMock(EntityManagerInterface::class);
        $this->tokenServiceMock = $this->createMock(TokenService::class);

        $this->purchaseService = new PurchaseService($this->emMock, $this->tokenServiceMock);
    }

    public function testPurchaseSuccess()
    {
        $this->createService();

        $testUser = new User();
        $testUser->setLogin('testLogin');

        $testGame = new Game();
        $testGame->setTitle('testTitle');

        $userRepositoryMock = $this->createMock(UserRepository::class);
        $gamesRepositoryMock = $this->createMock(GamesRepository::class);
        $purchasedGameRepositoryMock = $this->createMock(PurchasedGameRepository::class);

        $this->emMock
            ->expects($this->any())
            ->method('getRepository')
            ->willReturnOnConsecutiveCalls(
                $userRepositoryMock,
                $gamesRepositoryMock,
                $purchasedGameRepositoryMock
            );
        $userRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($testUser);
        $gamesRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($testGame);
        $purchasedGameRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $result = $this->purchaseService->purchase(1, 1);

        $this->assertEquals(
            array(
                'content' => [
                    'message' => 'Successfully purchased'
                ],
                'code' => Response::HTTP_OK
        ), $result);
    }

    public function testPurchaseGameAlreadyPurchased()
    {
        $this->createService();

        $testUser = new User();
        $testUser->setLogin('testLogin');

        $testGame = new Game();
        $testGame->setTitle('testTitle');

        $testPurchasedGame = new PurchasedGame();
        $testPurchasedGame->setUser($testUser);
        $testPurchasedGame->setGame($testGame);

        $userRepositoryMock = $this->createMock(UserRepository::class);
        $gamesRepositoryMock = $this->createMock(GamesRepository::class);
        $purchasedGameRepositoryMock = $this->createMock(PurchasedGameRepository::class);

        $this->emMock
            ->expects($this->any())
            ->method('getRepository')
            ->willReturnOnConsecutiveCalls(
                $userRepositoryMock,
                $gamesRepositoryMock,
                $purchasedGameRepositoryMock
            );
        $userRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($testUser);
        $gamesRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($testGame);
        $purchasedGameRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($testPurchasedGame);

        $result = $this->purchaseService->purchase(1, 1);

        $this->assertEquals(
            array(
                'content' => [
                    'message' => 'Game already purchased'
                ],
                'code' => Response::HTTP_FORBIDDEN
            ), $result);
    }

    public function testGetPurchasedGamesSuccess()
    {
        $this->createService();

        $testUser = new User();
        $testUser->setLogin('testLogin');
        $testUser->setToken('someToken');

        $testGame = new Game();
        $testGame->setTitle('testTitle');

        $testPurchasedGame = new PurchasedGame();
        $testPurchasedGame->setGame($testGame);
        $testPurchasedGame->setUser($testUser);

        $testUser->addPurchasedGame($testPurchasedGame);

        $userRepositoryMock = $this->createMock(UserRepository::class);

        $this->emMock
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($userRepositoryMock);
        $userRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($testUser);

        $fakeDecodedToken = new StdClass();
        $fakeDecodedToken->login = 'testLogin';

        $this->tokenServiceMock
            ->expects($this->once())
            ->method('decodeLongToken')
            ->willReturn($fakeDecodedToken);

        $result = $this->purchaseService->getPurchasedGames('someToken');

        $this->assertNotNull($result['content']);
        $this->assertNotNull($result['content'][0]);
        $this->assertEquals(0, $result['content'][0]['gameId']);
        $this->assertEquals('testTitle', $result['content'][0]['title']);
        $this->assertEquals(0.0, $result['content'][0]['hoursOfPlaying']);
        $this->assertEquals(Response::HTTP_OK, $result['code']);
    }
}