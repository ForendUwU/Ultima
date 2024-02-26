<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Game;
use App\Entity\PurchasedGame;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class PurchasedGameTest extends TestCase
{
    public function testCreateEmptyPurchasedGame(): void
    {
        $testPurchasedGame = new PurchasedGame();

        $this->assertNotNull($testPurchasedGame);
        $this->assertNotNull($testPurchasedGame->getBoughtAt());
        $this->assertNotNull($testPurchasedGame->getHoursOfPlaying());
        $this->assertNull($testPurchasedGame->getId());
    }

    public function testCreateNotEmptyPurchasedGame()
    {
        $testPurchasedGame = new PurchasedGame();

        $testPurchasedGame->setUser(new User());
        $testPurchasedGame->setGame(new Game());
        $testPurchasedGame->setHoursOfPlaying(4);

        $this->assertNotNull($testPurchasedGame->getUser());
        $this->assertNotNull($testPurchasedGame->getGame());
        $this->assertEquals(4, $testPurchasedGame->getHoursOfPlaying());
    }
}