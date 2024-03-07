<?php

namespace App\Tests\Unit\Service;

use App\Entity\Game;
use App\Entity\PurchasedGame;
use App\Entity\User;
use App\Repository\PurchasedGameRepository;
use App\Repository\UserRepository;
use App\Service\GetEntitiesService;
use App\Service\UserInfoService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class UserInfoServiceTest extends TestCase
{
    private UserInfoService $userInfoService;
    private $emMock;
    private $getEntitiesServiceMock;

    public function setUp(): void
    {
        $this->emMock = $this->createMock(EntityManagerInterface::class);
        $this->getEntitiesServiceMock = $this->createMock(GetEntitiesService::class);
        $this->userInfoService = new UserInfoService($this->emMock, $this->getEntitiesServiceMock);
    }

    public function testGetUserInfo()
    {
        $testUser = new User();
        $testUser->setPassword('test');
        $testUser->setToken('test');

        $repositoryMock = $this->createMock(UserRepository::class);

        $this->emMock
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($repositoryMock);
        $repositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($testUser);

        $result = $this->userInfoService->getUserInfo(0);

        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('login', $result);
        $this->assertArrayHasKey('nickname', $result);
        $this->assertArrayHasKey('balance', $result);
        $this->assertArrayHasKey('firstName', $result);
        $this->assertArrayHasKey('lastName', $result);
        $this->assertArrayHasKey('email', $result);
        $this->assertArrayHasKey('purchasedGames', $result);
        $this->assertEquals($testUser->getLogin(), $result['login']);
        $this->assertEquals($testUser->getNickname(), $result['nickname']);
        $this->assertEquals($testUser->getBalance(), $result['balance']);
        $this->assertEquals($testUser->getFirstName(), $result['firstName']);
        $this->assertEquals($testUser->getLastName(), $result['lastName']);
        $this->assertEquals($testUser->getEmail(), $result['email']);
        $this->assertEquals($testUser->getPurchasedGames(), $result['purchasedGames']);
    }

    public function testGetUsersMostPlayedGames()
    {
        $testUser = new User();
        $testUser->setLogin('testLogin');
        $testUser->setPassword('test');
        $testUser->setToken('test');

        $testGame1 = new Game();
        $testGame1->setTitle('testTitle1');
        $testGame2 = new Game();
        $testGame2->setTitle('testTitle2');

        $testPurchasedGame1 = new PurchasedGame();
        $testPurchasedGame1->setGame($testGame1);
        $testPurchasedGame1->setUser($testUser);
        $testPurchasedGame1->setHoursOfPlaying(1);

        $testPurchasedGame2 = new PurchasedGame();
        $testPurchasedGame2->setGame($testGame2);
        $testPurchasedGame2->setUser($testUser);
        $testPurchasedGame2->setHoursOfPlaying(2);

        $this->getEntitiesServiceMock
            ->expects($this->once())
            ->method('getUserByLogin')
            ->willReturn($testUser);

        $purchasedGameRepositoryMock = $this->createMock(PurchasedGameRepository::class);

        $this->emMock
            ->expects($this->any())
            ->method('getRepository')
            ->willReturn($purchasedGameRepositoryMock);
        $purchasedGameRepositoryMock
            ->expects($this->once())
            ->method('findBy')
            ->willReturn([$testPurchasedGame2, $testPurchasedGame1]);

        $result = $this->userInfoService->getUsersMostPlayedGames('testLogin');

        $this->assertNotEmpty($result);
        $this->assertArrayHasKey(0, $result);
        $this->assertArrayHasKey(1, $result);
        $this->assertNotEmpty($result[0]);
        $this->assertNotEmpty($result[1]);
        $this->assertArrayHasKey('title', $result[0]);
        $this->assertArrayHasKey('hoursOfPlaying', $result[0]);
        $this->assertArrayHasKey('title', $result[1]);
        $this->assertArrayHasKey('hoursOfPlaying', $result[1]);
        $this->assertEquals('testTitle2', $result[0]['title']);
        $this->assertEquals('2', $result[0]['hoursOfPlaying']);
        $this->assertEquals('testTitle1', $result[1]['title']);
        $this->assertEquals('1', $result[1]['hoursOfPlaying']);
    }
}
