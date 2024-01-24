<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Game;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

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
        $testGame->setTitle('Test GameUnitTest');
        $testGame->setDescription('Test Description');
        $testGame->setPrice(9.99);

        $this->assertNotNull($testGame->getPublishedAt());
        $this->assertNotNull($testGame->getPurchasedGames());
        $this->assertEquals('Test GameUnitTest', $testGame->getTitle());
        $this->assertEquals('Test Description', $testGame->getDescription());
        $this->assertEquals(9.99, $testGame->getPrice());
    }
}
