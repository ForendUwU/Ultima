<?php

namespace App\Tests\Unit\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\AuthorizationService;
use App\Services\TokenService;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\HttpFoundation\Response;

class AuthorizationServiceTest extends TestCase
{
    private AuthorizationService $authService;
    private $tokenServiceMock;
    private $emMock;

    public function createService(): void
    {
        $this->tokenServiceMock = $this->createMock(TokenService::class);
        $this->emMock = $this->createMock(EntityManagerInterface::class);
        $this->authService = new AuthorizationService($this->emMock, $this->tokenServiceMock);
    }

    public function testLoginSuccess()
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

        $this->tokenServiceMock
            ->expects($this->once())
            ->method('createToken')
            ->willReturn('test');

        $result = $this->authService->login('test', 'test');

        $this->assertEquals(Response::HTTP_OK, $result['code']);
        $this->assertNotEmpty($result['content']['token']);
        $this->assertEquals('test', $result['content']['token']);
    }

    public function testLoginUserDoesNotExist()
    {
        $this->createService();

        $repositoryMock = $this->createMock(UserRepository::class);

        $this->emMock
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($repositoryMock);
        $repositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $result = $this->authService->login('test', 'test');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $result['code']);
        $this->assertNotEmpty($result['content']['message']);
        $this->assertEquals('This user does not exist' , $result['content']['message']);
    }

    public function testLoginWrongPassword()
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

        $result = $this->authService->login('test', 'wrongPassword');

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $result['code']);
        $this->assertNotEmpty($result['content']['message']);
        $this->assertEquals('Wrong login or password', $result['content']['message']);
    }

    public function testRegisterSuccess()
    {
        $this->createService();

        $this->tokenServiceMock
            ->expects($this->once())
            ->method('createToken')
            ->willReturn('test');

        $result = $this->authService->register('test', 'test', 'test@email.com', 'test');

        $this->assertEquals(Response::HTTP_OK, $result['code']);
        $this->assertNotEmpty($result['content']['token']);
        $this->assertEquals('test', $result['content']['token']);
    }

    public function testRegisterUserWithThisLoginAlreadyExists()
    {
        $this->createService();

        $this->tokenServiceMock
            ->expects($this->once())
            ->method('createToken')
            ->willReturn('test');

        $exceptionMock = $this->createMock(UniqueConstraintViolationException::class);
        $this->emMock
            ->expects($this->once())
            ->method('flush')
            ->willThrowException($exceptionMock);

        $result = $this->authService->register('test', 'test', 'test@email.com', 'test');

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $result['code']);
        $this->assertNotEmpty($result['content']['message']);
        $this->assertEquals('This login is already in use', $result['content']['message']);
    }

    public function testLogoutSuccess()
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

        $fakeDecodedToken = new StdClass();
        $fakeDecodedToken->login = 'someLogin';

        $this->tokenServiceMock
            ->expects($this->once())
            ->method('decode')
            ->willReturn($fakeDecodedToken);

        $result = $this->authService->logout('test');

        $this->assertEquals(Response::HTTP_OK, $result['code']);
        $this->assertNotEmpty($result['content']['message']);
        $this->assertEquals('Logout successfully', $result['content']['message']);
    }

    public function testLogoutUserDoesNotExist()
    {
        $this->createService();

        $repositoryMock = $this->createMock(UserRepository::class);

        $this->emMock
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($repositoryMock);
        $repositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);

        $fakeDecodedToken = new StdClass();
        $fakeDecodedToken->login = 'someLogin';

        $this->tokenServiceMock
            ->expects($this->once())
            ->method('decode')
            ->willReturn($fakeDecodedToken);

        $result = $this->authService->logout('test');

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $result['code']);
        $this->assertNotEmpty($result['content']['message']);
        $this->assertEquals('User does not exist', $result['content']['message']);
    }

    public function testLogoutUserAlreadyUnauthorized()
    {
        $this->createService();

        $testUser = new User();
        $testUser->setPassword('test');
        $testUser->setToken('');

        $repositoryMock = $this->createMock(UserRepository::class);

        $this->emMock
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($repositoryMock);
        $repositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($testUser);

        $fakeDecodedToken = new StdClass();
        $fakeDecodedToken->login = 'someLogin';

        $this->tokenServiceMock
            ->expects($this->once())
            ->method('decode')
            ->willReturn($fakeDecodedToken);

        $result = $this->authService->logout('test');

        $this->assertEquals(Response::HTTP_FORBIDDEN, $result['code']);
        $this->assertNotEmpty($result['content']['message']);
        $this->assertEquals('User already unauthorized', $result['content']['message']);
    }
}
