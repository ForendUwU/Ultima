<?php

namespace App\Tests\Unit\Entity;

use App\Entity\PurchasedGame;
use App\Entity\User;
use App\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function validateLoginDataProvider(): array
    {
        return [
            'smallLogin' => ['small'],
            'bigLogin' => ['biggggggggggggggggggg'],
            'loginWithInvalidCharacters' => ['login123^']
        ];
    }
    public function validateNicknameDataProvider(): array
    {
        return [
            'smallNickname' => ['s'],
            'bigNickname' => ['bigggggggggggggggggggggggggggggggggggggggggggggggggggggggggggg'],
            'NicknameWithInvalidCharacters' => ['nickname123^']
        ];
    }


    public function testCreateEmptyUserEntity(): void
    {
        $testUser = new User();
        $this->assertNotNull($testUser);
        $this->assertNotNull($testUser->getCreatedAt());
        $this->assertNotNull($testUser->getBalance());
        $this->assertEquals(0, $testUser->getBalance());
        $this->assertNotNull($testUser->getPurchasedGames());
        $this->assertEquals(0, $testUser->getId());
    }

    public function testCreateNotEmptyUserEntity(): void
    {
        $testUser = new User();
        $testPurchasedGame = new PurchasedGame();

        $testUser->setLogin('testLogin');
        $testUser->setRoles(['ROLE_ADMIN']);
        $testUser->setPassword('testPassword');
        $testUser->setNickname('testNickname');
        $testUser->setFirstName('TestFirstName');
        $testUser->setLastName('TestLastName');
        $testUser->setEmail('test@email.com');
        $testUser->addPurchasedGame($testPurchasedGame);
        $testUser->setToken('Test token');
        $testUser->setBalance(2.10);

        $this->assertEquals('testLogin', $testUser->getLogin());
        $this->assertEquals('testLogin', $testUser->getUserIdentifier());
        $this->assertContains('ROLE_ADMIN', $testUser->getRoles());
        $this->assertContains($testPurchasedGame, $testUser->getPurchasedGames());
        $this->assertEquals('Test token', $testUser->getToken());
        $this->assertEquals('testPassword', $testUser->getPassword());
        $this->assertEquals('testNickname', $testUser->getNickname());
        $this->assertEquals('TestFirstName', $testUser->getFirstName());
        $this->assertEquals('TestLastName', $testUser->getLastName());
        $this->assertEquals('test@email.com', $testUser->getEmail());
        $this->assertEquals(2.10, $testUser->getBalance());

        $testUser->removePurchasedGame($testPurchasedGame);

        $this->assertTrue($testUser->getPurchasedGames()->isEmpty());
    }

    /**
     *  @dataProvider validateLoginDataProvider
     */
    public function testValidateLogin($login)
    {
        $testUser = new User();

        if ($login === 'small') {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage('Login must contain 6 or more characters');
            $testUser->setLogin($login);
        } else if ($login === 'biggggggggggggggggggg') {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage('Login must contain less than 20 characters');
            $testUser->setLogin($login);
        } else if ($login === 'login123^') {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage('Login must contain only letters, numbers and "!", "~", "_", "&", "*", "%", "@", "$" characters');
            $testUser->setLogin($login);
        }
    }

    /**
     *  @dataProvider validateNicknameDataProvider
     */
    public function testValidateNickname($nickname)
    {
        $testUser = new User();

        if ($nickname === 's') {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage('Nickname must contain 2 or more characters');
            $testUser->setNickname($nickname);
        } else if ($nickname === 'bigggggggggggggggggggggggggggggggggggggggggggggggggggggggggggg') {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage('Nickname must contain less than 50 characters');
            $testUser->setNickname($nickname);
        } else if ($nickname === 'nickname123^') {
            $this->expectException(ValidationException::class);
            $this->expectExceptionMessage('Nickname must contain only letters, numbers and "!", "~", "_", "&", "*", "%", "@", "$" characters');
            $testUser->setNickname($nickname);
        }
    }
}
