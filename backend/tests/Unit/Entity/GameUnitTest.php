<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Game;
use App\Entity\PurchasedGame;
use PHPUnit\Framework\TestCase;

class GameUnitTest extends TestCase
{
    public function testCreateEmptyGameEntity(): void
    {
        $testGame = new Game();

        $this->assertNotNull($testGame);
        $this->assertNotNull($testGame->getPublishedAt());
        $this->assertNotNull($testGame->getPurchasedGames());
    }

    public function testCreateNotEmptyGameEntity(): void
    {
        $testGame = new Game();
        $testPurchasedGame = new PurchasedGame();

        $testGame->setTitle('Test GameUnitTest');
        $testGame->setDescription('Test Description');
        $testGame->setPrice(9.99);
        $testGame->addPurchasedGame($testPurchasedGame);

        $this->assertNotNull($testGame->getPublishedAt());
        $this->assertEquals($testPurchasedGame, $testGame->getPurchasedGames()->getValues()[0]);
        $this->assertEquals('Test GameUnitTest', $testGame->getTitle());
        $this->assertEquals('Test Description', $testGame->getDescription());
        $this->assertEquals(9.99, $testGame->getPrice());

        $testGame->removePurchasedGame($testPurchasedGame);
        $this->assertEmpty($testGame->getPurchasedGames()->getValues());
    }
}
