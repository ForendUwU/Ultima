<?php

namespace App\Tests\Unit\Service;

use App\Exceptions\ValidationException;
use App\Repository\UserRepository;
use App\Service\AuthorizationService;
use App\Service\TokenService;
use App\Tests\Traits\CreateUserTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthorizationServiceTest extends TestCase
{
    use CreateUserTrait;

    public static $emMock;

    private AuthorizationService $authService;
    private $tokenServiceMock;
    private $userPasswordHasherMock;

    public function setUp(): void
    {
        static::$emMock = $this->createMock(EntityManagerInterface::class);

        $this->tokenServiceMock = $this->createMock(TokenService::class);
        $this->userPasswordHasherMock = $this->createMock(UserPasswordHasherInterface::class);
        $this->authService = new AuthorizationService(
            static::$emMock,
            $this->tokenServiceMock,
            $this->userPasswordHasherMock
        );
    }

    public function loginDataProvider(): array
    {
        return [
            'success' => ['testPassword'],
            'wrong password' => ['wrongPassword']
        ];
    }

    public function logoutDataProvider(): array
    {
        return [
            'success' => [true],
            'user already unauthorized' => [false]
        ];
    }

    public function validatePasswordDataProvider(): array
    {
        return [
            'validPassword' => ['validPassword123!'],
            'tooSmallPassword' => ['small'],
            'tooBigPassword' => ['biggggggggggggggggggggggggggggggggggggggggggggggggggggggggg'],
            'passwordWithInvalidSymbols' => ['^^^^^^'],
            'passwordWithoutDigits' => ['password'],
            'passwordWithoutSymbols' => ['password1'],
        ];
    }

    /**
     *  @dataProvider loginDataProvider
     */
    public function testLogin($password)
    {
        $testUser = $this->createUser();

        $userRepositoryMock = $this->createMock(UserRepository::class);
        $this->setUserRepositoryAsReturnFromEntityManager($userRepositoryMock);
        $this->setTestUserAsReturnFromRepositoryMockByLogin($userRepositoryMock, $testUser);

        if ($password === 'testPassword') {
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
            $this->assertEquals('test', $result);
        } else if ($password === 'wrongPassword') {
            $this->userPasswordHasherMock
                ->expects($this->any())
                ->method('isPasswordValid')
                ->willReturn(false);

            $this->expectException(\Exception::class);
            $this->expectExceptionMessage('Wrong login or password');
            $this->authService->login('test', $password);
        }
    }

    /**
     *  @dataProvider logoutDataProvider
     */
    public function testLogout($token)
    {
        $testUser = $this->createUser();

        if (!$token)
            $testUser->setToken(null);

        $userRepositoryMock = $this->createMock(UserRepository::class);
        $this->setUserRepositoryAsReturnFromEntityManager($userRepositoryMock);
        $this->setTestUserAsReturnFromRepositoryMockById($userRepositoryMock, $testUser);

        if ($token) {
            $result = $this->authService->logout($testUser->getId());

            $this->assertNotNull($result);
            $this->assertEquals('Logout successfully', $result);
        }  elseif (!$token) {
            $this->expectException(\Exception::class);
            $this->expectExceptionMessage('User already unauthorized');

            $this->authService->logout($testUser->getId());
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
     *  @dataProvider validatePasswordDataProvider
     */
    public function testValidatePassword($testPassword)
    {
        if ($testPassword === 'validPassword123!') {
            $this->assertTrue($this->authService->validatePassword($testPassword));
        } else if ($testPassword === 'small') {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage('Password must contain 6 or more characters');
            $this->authService->validatePassword($testPassword);
        } else if ($testPassword === 'biggggggggggggggggggggggggggggggggggggggggggggggggggggggggg') {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage('Password must contain less than 50 characters');
            $this->authService->validatePassword($testPassword);
        } else if ($testPassword === '^^^^^^') {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage('Password must contain only letters, numbers and "!", "~", "_", "&", "*", "%", "@", "$" characters');
            $this->authService->validatePassword($testPassword);
        } else if ($testPassword === 'password') {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage('Password must contain at least one number');
            $this->authService->validatePassword($testPassword);
        } else if ($testPassword === 'password1') {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage('Password must contain at least one of this symbols "!", "~", "_", "&", "*", "%", "@", "$"');
            $this->authService->validatePassword($testPassword);
        }
    }
}
