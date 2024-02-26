<?php

namespace App\Tests\Unit\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\AuthorizationService;
use App\Service\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthorizationServiceTest extends TestCase
{
    private AuthorizationService $authService;
    private $tokenServiceMock;
    private $emMock;
    private $userPasswordHasherMock;

    public function setUp(): void
    {
        $this->tokenServiceMock = $this->createMock(TokenService::class);
        $this->emMock = $this->createMock(EntityManagerInterface::class);
        $this->userPasswordHasherMock = $this->createMock(UserPasswordHasherInterface::class);
        $this->authService = new AuthorizationService(
            $this->emMock,
            $this->tokenServiceMock,
            $this->userPasswordHasherMock
        );
    }

    public function loginDataProvider(): array
    {
        $expectedToken = 'test';
        $password = 'test';
        $wrongPassword = 'wrongPassword';

        $testUser = new User();
        $testUser->setPassword($password);
        $testUser->setToken($expectedToken);

        return [
            'success' => [$testUser, $password, $expectedToken],
            'user does not exist' => [null, $password, $expectedToken],
            'wrong password' => [$testUser, $wrongPassword,  $expectedToken]
        ];
    }

    public function logoutDataProvider(): array
    {
        $testUser = new User();
        $testUser->setPassword('test');
        $testUser->setToken('test');

        $testUserWithoutToken = new User();
        $testUserWithoutToken->setPassword('test');
        $testUserWithoutToken->setToken('test');

        return [
            'success' => [$testUser],
            'user does not exist' => [null],
            'user already unauthorized' => [$testUserWithoutToken]
        ];
    }

    /**
     *  @dataProvider loginDataProvider
     */
    public function testLogin1($testUser, $password, $expectedToken)
    {
        $repositoryMock = $this->createMock(UserRepository::class);

        $this->emMock
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($repositoryMock);
        $repositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($testUser);

        if ($testUser && $password == 'test') {
            $this->tokenServiceMock
                ->expects($this->once())
                ->method('createToken')
                ->willReturn('test');

            $this->userPasswordHasherMock
                ->expects($this->any())
                ->method('isPasswordValid')
                ->willReturn(true);

            $result = $this->authService->login('test', $password);

            $this->assertNotNull($result);
            $this->assertEquals($expectedToken, $result);
        } elseif (!$testUser) {
            $this->expectException(\Exception::class);
            $this->expectExceptionMessage('This user does not exist');
            $this->authService->login('test', $password);
        } else {
            $this->userPasswordHasherMock
                ->expects($this->any())
                ->method('isPasswordValid')
                ->willReturn(false);

            $this->expectException(\Exception::class);
            $this->expectExceptionMessage('Wrong login or password');
            $this->authService->login('test', $password);
        }
    }

    public function testRegisterSuccess()
    {
        $this->tokenServiceMock
            ->expects($this->once())
            ->method('createToken')
            ->willReturn('test');

        $result = $this->authService->register('testLogin', 'testPassword1!', 'test@email.com', 'test');

        $this->assertNotNull($result);
        $this->assertEquals('test', $result);
    }

    /**
     *  @dataProvider logoutDataProvider
     */
    public function testLogout($testUser)
    {
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
        $fakeDecodedToken->login = 'testLogin';

        $this->tokenServiceMock
            ->expects($this->once())
            ->method('decodeLongToken')
            ->willReturn($fakeDecodedToken);

        if ($testUser) {
            $result = $this->authService->logout('test');

            $this->assertNotNull($result);
            $this->assertEquals('Logout successfully', $result);
        } elseif (empty($testUser)) {
            $this->expectException(\Exception::class);
            $this->expectExceptionMessage('User does not exist');

            $this->authService->logout('test');
        }  elseif (!$testUser->getToken()) {
            $this->expectException(\Exception::class);
            $this->expectExceptionMessage('User already unauthorized');

            $this->authService->logout('test');
        }
    }
}
