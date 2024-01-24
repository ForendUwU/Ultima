<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Game;
use App\Entity\PurchasedGame;
use App\Entity\User;
use Monolog\Test\TestCase;

class PurchasedGameTest extends TestCase
{
    public function testCreateEmptyPurchasedGame(): void
    {
        $testPurchasedGame = new PurchasedGame();

        $this->assertNotNull($testPurchasedGame);
        $this->assertNotNull($testPurchasedGame->getBoughtAt());
        $this->assertNotNull($testPurchasedGame->getHoursOfPlaying());
    }

    public function testCreateNotEmptyPurchasedGame()
    {
        $testPurchasedGame = new PurchasedGame();

        $testPurchasedGame->setUser(new User());
        $testPurchasedGame->setGame(new Game());

        $this->assertNotNull($testPurchasedGame->getUser());
        $this->assertNotNull($testPurchasedGame->getGame());
    }
}