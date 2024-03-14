<?php

namespace App\Tests\Unit\Service;

use App\Repository\PurchasedGameRepository;
use App\Repository\UserRepository;
use App\Service\UserInfoService;
use App\Tests\Traits\CreateGameTrait;
use App\Tests\Traits\CreatePurchasedGameTrait;
use App\Tests\Traits\CreateUserTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserInfoServiceTest extends TestCase
{
    use CreateUserTrait, CreateGameTrait, CreatePurchasedGameTrait;

    private UserInfoService $userInfoService;
    public static $emMock;
    private $userPasswordHasherMock;

    public function setUp(): void
    {
        static::$emMock = $this->createMock(EntityManagerInterface::class);
        $this->userPasswordHasherMock = $this->createMock(UserPasswordHasherInterface::class);
        $this->userInfoService = new UserInfoService(
            static::$emMock,
            $this->userPasswordHasherMock
        );
    }

    public function testGetUserInfo()
    {
        $testUser = $this->createUser();

        $userRepositoryMock = $this->createMock(UserRepository::class);

        $this->setUserRepositoryAsReturnFromEntityManager($userRepositoryMock);
        $this->setTestUserAsReturnFromRepositoryMock($userRepositoryMock, $testUser);

        $result = $this->userInfoService->getUserInfo($testUser->getLogin());

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
        $testUser = $this->createUser();

        $testGame1 = $this->createGame(title: 'testTitle1');
        $testGame2 = $this->createGame(title: 'testTitle2');

        $testPurchasedGame1 = $this->createPurchasedGame($testUser, $testGame1, 1);
        $testPurchasedGame2 = $this->createPurchasedGame($testUser, $testGame2, 2);

        $userRepositoryMock = $this->createMock(UserRepository::class);
        $purchasedGameRepositoryMock = $this->createMock(PurchasedGameRepository::class);

        static::$emMock
            ->expects($this->any())
            ->method('getRepository')
            ->willReturnOnConsecutiveCalls($userRepositoryMock, $purchasedGameRepositoryMock);

        $this->setTestUserAsReturnFromRepositoryMock($userRepositoryMock, $testUser);

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

    public function testUpdateUserInfo()
    {
        $testUser = $this->createUser();

        $testData = [
            'nickname' => 'anotherNickname',
            'password' => 'anotherPassword',
            'firstName' => 'anotherFirstName',
            'lastName' => 'anotherLastName',
            'email' => 'anotherEmail'
        ];

        $userRepositoryMock = $this->createMock(UserRepository::class);
        $this->setUserRepositoryAsReturnFromEntityManager($userRepositoryMock);
        $this->setTestUserAsReturnFromRepositoryMock($userRepositoryMock, $testUser);

        $result = $this->userInfoService->updateUserInfo($testUser->getLogin(), $testData);

        $this->assertEquals($testData['nickname'], $result->getNickname());
        $this->assertEquals($testData['password'], $result->getPassword());
        $this->assertEquals($testData['firstName'], $result->getFirstName());
        $this->assertEquals($testData['lastName'], $result->getLastName());
        $this->assertEquals($testData['email'], $result->getEmail());
    }
}
