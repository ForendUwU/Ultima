<?php

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GamesResourceTest extends KernelTestCase
{
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }
    public function getGamesTest(): void
    {
        $this->client->request('GET', '/banks/list');

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertArrayHasKey('id', $decodedResponse[0]);
        $this->assertArrayHasKey('idCountry', $decodedResponse[0]);
        $this->assertArrayHasKey('name', $decodedResponse[0]);
    }
}