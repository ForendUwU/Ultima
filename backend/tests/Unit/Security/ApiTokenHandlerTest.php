<?php

namespace App\Tests\Unit\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\ApiTokenHandler;
use App\Service\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\ExpiredException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class ApiTokenHandlerTest extends TestCase
{
    public function testGetUserBadgeFromSuccess(): void
    {
        $testUser = new User();
        $testUser->setLogin('test login');

        $testToken = 'Some token';

        $emMock = $this->createMock(EntityManagerInterface::class);
        $repositoryMock = $this->createMock(UserRepository::class);
        $tokenServiceMock = $this->createMock(TokenService::class);

        $repositoryMock->expects(self::once())
            ->method('findOneBy')
            ->willReturn($testUser);
        $emMock->expects(self::once())
            ->method('getRepository')
            ->willReturn($repositoryMock);

        $mockedDecodedToken = new StdClass();
        $fakeTokenCreationDate = new \DateTimeImmutable();
        $mockedDecodedToken->tokenCreationDate = $fakeTokenCreationDate->format('Y-m-d H:i:s');

        $tokenServiceMock->expects(self::once())
            ->method('decode')
            ->willReturn($mockedDecodedToken);

        $apiTokenHandler = new ApiTokenHandler($emMock, $tokenServiceMock);

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
        $tokenServiceMock = $this->createMock(TokenService::class);

        $repositoryMock->expects(self::once())
            ->method("findOneBy")
            ->willReturn(null);
        $emMock->expects(self::once())
            ->method("getRepository")
            ->willReturn($repositoryMock);

        $apiTokenHandler = new ApiTokenHandler($emMock, $tokenServiceMock);

        $this->expectException(BadCredentialsException::class);

        $apiTokenHandler->getUserBadgeFrom($testToken);
    }

    public function testGetUserBadgeFromTokenExpired(): void
    {
        $testUser = new User();
        $testUser->setLogin('test login');

        $testToken = 'Some token';

        $emMock = $this->createMock(EntityManagerInterface::class);
        $repositoryMock = $this->createMock(UserRepository::class);
        $tokenServiceMock = $this->createMock(TokenService::class);

        $repositoryMock->expects(self::once())
            ->method('findOneBy')
            ->willReturn($testUser);
        $emMock->expects(self::once())
            ->method('getRepository')
            ->willReturn($repositoryMock);

        $mockedDecodedToken = new StdClass();
        $fakeTokenCreationDate = new \DateTimeImmutable();
        $mockedDecodedToken->tokenCreationDate = $fakeTokenCreationDate->modify('-1 day')->format('Y-m-d H:i:s');

        $tokenServiceMock->expects(self::once())
            ->method('decode')
            ->willReturn($mockedDecodedToken);

        $apiTokenHandler = new ApiTokenHandler($emMock, $tokenServiceMock);

        $this->expectException(ExpiredException::class);
        $this->expectExceptionMessage('Token expired');

        $apiTokenHandler->getUserBadgeFrom($testToken);
    }
}