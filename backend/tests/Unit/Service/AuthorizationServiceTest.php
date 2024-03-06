<?php

namespace App\Tests\Unit\Service;

use App\Entity\User;
use App\Exceptions\ValidationException;
use App\Repository\UserRepository;
use App\Service\AuthorizationService;
use App\Service\GetEntitiesService;
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
    private $getEntitiesServiceMock;

    public function setUp(): void
    {
        $this->tokenServiceMock = $this->createMock(TokenService::class);
        $this->emMock = $this->createMock(EntityManagerInterface::class);
        $this->userPasswordHasherMock = $this->createMock(UserPasswordHasherInterface::class);
        $this->getEntitiesServiceMock = $this->createMock(GetEntitiesService::class);
        $this->authService = new AuthorizationService(
            $this->emMock,
            $this->tokenServiceMock,
            $this->userPasswordHasherMock,
            $this->getEntitiesServiceMock
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
            'user already unauthorized' => [$testUserWithoutToken]
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
    public function testLogin($testUser, $password, $expectedToken)
    {
        $this->getEntitiesServiceMock
            ->expects($this->once())
            ->method('getUserByLogin')
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
        $this->getEntitiesServiceMock
            ->expects($this->once())
            ->method('getUserByLogin')
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
        }  elseif (!$testUser->getToken()) {
            $this->expectException(\Exception::class);
            $this->expectExceptionMessage('User already unauthorized');

            $this->authService->logout('test');
        }
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
