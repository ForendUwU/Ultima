<?php

namespace App\Tests\Unit\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserInfoService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class UserInfoServiceTest extends TestCase
{
    private UserInfoService $userInfoService;
    private $emMock;

    public function setUp(): void
    {
        $this->emMock = $this->createMock(EntityManagerInterface::class);
        $this->userInfoService = new UserInfoService($this->emMock);
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
}
