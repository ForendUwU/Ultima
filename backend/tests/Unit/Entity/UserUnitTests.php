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
        $this->assertNotNull($testUser->getId());
        $this->assertNotNull($testUser->getCreatedAt());
        $this->assertNotNull($testUser->getBalance());
        $this->assertEquals(0, $testUser->getBalance());
        $this->assertNotNull($testUser->getPurchasedGames());
        $this->assertNotNull($testUser->getTokens());
    }
}
