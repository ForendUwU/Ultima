<?php

namespace App\Tests\Unit\Entity;

use App\Entity\PurchasedGame;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserUnitTest extends TestCase
{
    public function testCreateEmptyUserEntity(): void
    {
        $testUser = new User();
        $this->assertNotNull($testUser);
        $this->assertNotNull($testUser->getCreatedAt());
        $this->assertNotNull($testUser->getBalance());
        $this->assertEquals(0, $testUser->getBalance());
        $this->assertNotNull($testUser->getPurchasedGames());
    }

    public function testCreateNotEmptyUserEntity(): void
    {
        $testUser = new User();
        $testPurchasedGame = new PurchasedGame();

        $testUser->setLogin('Test Login');
        $testUser->setRoles(['ROLE_ADMIN']);
        $testUser->setPassword('testPassword');
        $testUser->setNickname('Test Nickname');
        $testUser->setFirstName('Test FirstName');
        $testUser->setLastName('Test LastName');
        $testUser->setEmail('test@email.com');
        $testUser->addPurchasedGame($testPurchasedGame);
        $testUser->setToken('Test token');

        $this->assertEquals('Test Login', $testUser->getLogin());
        $this->assertContains('ROLE_ADMIN', $testUser->getRoles());
        $this->assertContains($testPurchasedGame, $testUser->getPurchasedGames());
        $this->assertEquals('Test token', $testUser->getToken());
        $this->assertEquals('testPassword', $testUser->getPassword());
        $this->assertEquals('Test Nickname', $testUser->getNickname());
        $this->assertEquals('Test FirstName', $testUser->getFirstName());
        $this->assertEquals('Test LastName', $testUser->getLastName());
        $this->assertEquals('test@email.com', $testUser->getEmail());
        
        $testUser->removePurchasedGame($testPurchasedGame);

        $this->assertTrue($testUser->getPurchasedGames()->isEmpty());
    }
}
