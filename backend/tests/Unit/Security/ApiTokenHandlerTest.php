<?php

namespace App\Tests\Unit\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\ApiTokenHandler;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class ApiTokenHandlerTest extends TestCase
{
    public function testGetUserBadgeFrom(): void
    {
        $testUser = new User();
        $testUser->setLogin('test login');

        $testToken = 'Some token';

        $emMock = $this->createMock(EntityManagerInterface::class);
        $repositoryMock = $this->createMock(UserRepository::class);

        $repositoryMock->expects(self::once())
            ->method('findOneBy')
            ->willReturn($testUser);
        $emMock->expects(self::once())
            ->method('getRepository')
            ->willReturn($repositoryMock);

        $apiTokenHandler = new ApiTokenHandler($emMock);

        $expectedUserBadge = new UserBadge($testUser->getLogin());
        $this->assertEquals($expectedUserBadge, $apiTokenHandler->getUserBadgeFrom($testToken));
    }

    public function testGetUserBadgeFromBadCredentials(): void
    {
        $testUser = new User();
        $testUser->setLogin('test login');

        $testToken = 'Some token';

        $emMock = $this->createMock(EntityManagerInterface::class);
        $repositoryMock = $this->createMock(UserRepository::class);

        $repositoryMock->expects(self::once())
            ->method("findOneBy")
            ->willReturn(null);
        $emMock->expects(self::once())
            ->method("getRepository")
            ->willReturn($repositoryMock);

        $apiTokenHandler = new ApiTokenHandler($emMock);

        $this->expectException(BadCredentialsException::class);

        $apiTokenHandler->getUserBadgeFrom($testToken);
    }
}