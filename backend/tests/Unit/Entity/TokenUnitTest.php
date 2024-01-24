<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Token;
use App\Entity\User;
use Firebase\JWT\JWT;
use Monolog\Test\TestCase;

class TokenUnitTest extends TestCase
{
    public function testCreateEmptyToken()
    {
        $testToken = new Token();

        $this->assertNotNull($testToken);
        $this->assertNotNull($testToken->getExpiresAt());
    }

    public function testCreateNotEmptyToken()
    {
        $testToken = new Token(); //Best comment

        $testToken->setOwnedBy(new User());
        $testToken->setToken('Test token');
        $testToken->setScopes(['Test scope']);

        $this->assertEquals('Test token', $testToken->getToken());
        $this->assertContains('Test scope', $testToken->getScopes());
        $this->assertNotNull($testToken->getOwnedBy());
    }
}