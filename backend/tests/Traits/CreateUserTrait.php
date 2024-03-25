<?php

namespace App\Tests\Traits;

use App\Entity\User;

trait CreateUserTrait
{
    public static function createUser(
        $login = 'testLogin',
        $nickname = 'testNickname',
        $balance = 0,
        $email = 'testEmail',
        $firstName = 'testFirstName',
        $lastName = 'testLastName',
        $password = 'testPassword',
        $token = 'testToken'
    ): User {
        $testUser = new User();
        $testUser->setLogin($login);
        $testUser->setNickname($nickname);
        $testUser->setBalance($balance);
        $testUser->setEmail($email);
        $testUser->setFirstName($firstName);
        $testUser->setLastName($lastName);
        $testUser->setPassword($password);
        $testUser->setToken($token);

        return $testUser;
    }

    public static function addTokenToUser(User $user): string
    {
        $token = static::$tokenService->createToken($user);
        $user->setToken($token);
        return $token;
    }

    public static function setUserRepositoryAsReturnFromEntityManager($userRepositoryMock): void
    {
        static::$emMock
            ->expects(static::once())
            ->method('getRepository')
            ->willReturn($userRepositoryMock);
    }

    public static function setTestUserAsReturnFromRepositoryMockByLogin($userRepositoryMock, $testUser): void
    {
        $userRepositoryMock
            ->expects(static::once())
            ->method('findByLogin')
            ->willReturn($testUser);
    }

    public static function setTestUserAsReturnFromRepositoryMockById($userRepositoryMock, $testUser): void
    {
        $userRepositoryMock
            ->expects(static::once())
            ->method('findById')
            ->willReturn($testUser);
    }
}