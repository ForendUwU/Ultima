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

    public function testCreateGameEntity(): void
    {
        $testGame = new Game();
        $this->assertNotNull($testGame);
        $this->assertNotNull($testGame->getPublishedAt());
        $this->assertNotNull($testGame->getPurchasedGames());
    }
}
