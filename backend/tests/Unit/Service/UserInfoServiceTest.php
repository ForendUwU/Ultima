<?php

namespace App\Tests\Unit\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\AuthorizationService;
use App\Services\UserInfoService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class UserInfoServiceTest extends TestCase
{
    private UserInfoService $userInfoService;
    private $emMock;

    public function createService(): void
    {
        $this->emMock = $this->createMock(EntityManagerInterface::class);
        $this->userInfoService = new UserInfoService($this->emMock);
    }

    public function testGetUserInfo()
    {
        $this->createService();

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
        $this->assertNotEmpty($result['content']);
        $this->assertArrayHasKey('login', $result['content']);
        $this->assertArrayHasKey('nickname', $result['content']);
        $this->assertArrayHasKey('balance', $result['content']);
        $this->assertArrayHasKey('firstName', $result['content']);
        $this->assertArrayHasKey('lastName', $result['content']);
        $this->assertArrayHasKey('email', $result['content']);
        $this->assertArrayHasKey('purchasedGames', $result['content']);
    }
}
