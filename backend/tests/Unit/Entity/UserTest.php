<?php

namespace App\Tests\Unit\Entity;

use App\Entity\PurchasedGame;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
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
}
