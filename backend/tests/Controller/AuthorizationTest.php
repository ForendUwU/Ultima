<?php

namespace App\Tests\Controller;

use App\Entity\Game;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthorizationTest extends WebTestCase
{
    protected KernelBrowser $client;
    protected EntityManager $manager;

    protected function setUp() : void
    {

    }

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
        $testGame->setTitle('Test Game');
        $testGame->setDescription('Test Description');
        $testGame->setPrice(9.99);

        $this->assertNotNull($testGame->getPublishedAt());
        $this->assertNotNull($testGame->getPurchasedGames());
        $this->assertEquals('Test Game', $testGame->getTitle());
        $this->assertEquals('Test Description', $testGame->getDescription());
        $this->assertEquals(9.99, $testGame->getPrice());
    }
}
