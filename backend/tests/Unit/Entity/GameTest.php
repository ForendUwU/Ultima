<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Game;
use App\Entity\PurchasedGame;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    public function testCreateEmptyGameEntity(): void
    {
        $testGame = new Game();

        $this->assertNotNull($testGame);
        $this->assertNotNull($testGame->getPublishedAt());
        $this->assertNotNull($testGame->getPurchasedGames());
        $this->assertNotNull($testGame->getID());
    }

    public function testCreateNotEmptyGameEntity(): void
    {
        $testGame = new Game();
        $testPurchasedGame = new PurchasedGame();

        $testGame->setTitle('Test GameTest');
        $testGame->setDescription('Test Description');
        $testGame->setPrice(9.99);
        $testGame->addPurchasedGame($testPurchasedGame);

        $this->assertNotNull($testGame->getPublishedAt());
        $this->assertEquals($testPurchasedGame, $testGame->getPurchasedGames()->getValues()[0]);
        $this->assertEquals('Test GameTest', $testGame->getTitle());
        $this->assertEquals('Test Description', $testGame->getDescription());
        $this->assertEquals(9.99, $testGame->getPrice());

        $testGame->removePurchasedGame($testPurchasedGame);
        $this->assertEmpty($testGame->getPurchasedGames()->getValues());
    }
}
