<?php

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class UserUnitTests extends TestCase
{
    protected KernelBrowser $client;
    protected EntityManager $manager;

    public function testCreateEmptyUserEntity(): void
    {
        $testUser = new User();
        $this->assertNotNull($testUser);
        $this->assertNotNull($testUser->getCreatedAt());
        $this->assertNotNull($testUser->getBalance());
        $this->assertEquals(0, $testUser->getBalance());
        $this->assertNotNull($testUser->getPurchasedGames());
        $this->assertNotNull($testUser->getTokens());
    }

    public function testCreateNotEmptyUserEntity(): void
    {
        $testUser = new User();
        $testUser->setLogin('Test Login');
        $testUser->setRoles(['ROLE_ADMIN']);
        $testUser->setPassword('testPassword');
        $testUser->setNickname('Test Nickname');
        $testUser->setFirstName('Test FirstName');
        $testUser->setLastName('Test LastName');
        $testUser->setEmail('test@email.com');

        $this->assertEquals('Test Login', $testUser->getLogin());
        $this->assertContains('ROLE_ADMIN', $testUser->getRoles());
        $this->assertEquals('testPassword', $testUser->getPassword());
        $this->assertEquals('Test Nickname', $testUser->getNickname());
        $this->assertEquals('Test FirstName', $testUser->getFirstName());
        $this->assertEquals('Test LastName', $testUser->getLastName());
        $this->assertEquals('test@email.com', $testUser->getEmail());
    }
}
