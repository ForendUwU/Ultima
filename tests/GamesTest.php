<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class GamesTest extends WebTestCase
{
    protected KernelBrowser $client;
    protected function setUp() : void
    {
        $this->client = static::createClient();

    }
    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function testSomething(): void
    {
        $this->client->request('GET', '/api/games');

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);
        dump($decodedResponse);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
