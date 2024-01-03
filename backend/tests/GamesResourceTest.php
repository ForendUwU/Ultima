<?php

namespace App\Tests;

use App\Entity\Game;
use App\Factory\GameFactory;
use App\Factory\UserFactory;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class GamesResourceTest extends WebTestCase
{
    protected KernelBrowser $client;
    protected EntityManager $manager;

    protected function setUp() : void
    {
        $this->client = static::createClient();
        $this->manager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
        $metaData = $this->manager->getMetadataFactory()->getAllMetadata();
        $tool = new SchemaTool($this->manager);
        $tool->dropSchema($metaData);
        $tool->createSchema($metaData);
    }

    private function authorize(): void
    {
        UserFactory::createOne([
            'login' => 'test_login',
            'password' => 'pass'
        ]);

        $this->client->jsonRequest(
            'POST',
            'https://localhost/login',
            [
                'login' => 'test_login',
                'password' => 'pass'
            ]
        );
    }

    public function testGetGamesCollection(): void
    {
        GameFactory::createMany(5);
        $this->client->jsonRequest(
            'GET',
            'https://localhost/api/games'
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertSame(array_keys($decodedResponse[0]), [
            'id',
            'title',
            'description',
            'price',
            'publishedAt',
            'purchasedGames'
        ]);
    }

    public function testPostNewGameSuccess(): void
    {
        $this->authorize();
        $this->client->jsonRequest(
            'POST',
            'https://localhost/api/games',
            [
                'title' => 'test_game',
                'description' => 'test_description',
                'price' => 99,
                'purchasedGames' => []
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertSame(array_keys($decodedResponse), [
            'id',
            'title',
            'description',
            'price',
            'publishedAt',
            'purchasedGames'
        ]);
    }

    public function testPostNewGameUnauthorized(): void
    {
        $this->client->jsonRequest(
            'POST',
            'https://localhost/api/games',
            [
                'title' => 'test_game',
                'description' => 'test_description',
                'price' => 99,
                'purchasedGames' => []
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertArrayHasKey('detail', $decodedResponse);
        $this->assertEquals(
            'Full authentication is required to access this resource.',
            $decodedResponse['detail']
        );
    }

    public function testPostNewGameInvalidInput(): void
    {
        $this->authorize();
        $this->client->jsonRequest(
            'POST',
            'https://localhost/api/games',
            [
                "title" => 123,
                "description" => 123,
                "price" => 0,
                "purchasedGames" => []
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertArrayHasKey('detail', $decodedResponse);
        $this->assertEquals(
            'The type of the "title" attribute must be "string", "integer" given.',
            $decodedResponse['detail']
        );
    }

    public function testPostNewUnprocessableEntity(): void
    {
        $this->authorize();
        $this->client->jsonRequest(
            'POST',
            'https://localhost/api/games',
            [
                'title' => 'Game0',
                'description' => 'test_description',
                'price' => 99,
                'purchasedGames' => []
            ]
        );

        $this->client->jsonRequest(
            'POST',
            'https://localhost/api/games',
            [
                'title' => 'Game0',
                'description' => 'test_description',
                'price' => 99,
                'purchasedGames' => []
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertArrayHasKey('violations', $decodedResponse);
        $this->assertArrayHasKey('message', $decodedResponse['violations'][0]);
        $this->assertEquals(
            'Game with this title already exists',
            $decodedResponse['violations'][0]['message']
        );
    }

    public function testGetGameSuccess(): void
    {
        GameFactory::createMany(5);
        $this->client->jsonRequest(
            'GET',
            'https://localhost/api/games/1'
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertSame(array_keys($decodedResponse), [
            'id',
            'title',
            'description',
            'price',
            'publishedAt',
            'purchasedGames'
        ]);
    }

    public function testGetGameResourceNotFound(): void
    {
        GameFactory::createMany(5);
        $this->client->jsonRequest(
            'GET',
            'https://localhost/api/games/6'
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertEquals('Not Found', $decodedResponse['detail']);
    }

    public function testDeleteGameSuccess(): void
    {
        GameFactory::createMany(5);
        $this->client->jsonRequest(
            'DELETE',
            'https://localhost/api/games/5'
        );

        $response = $this->client->getResponse();
        $deletedGame = $this->manager->getRepository(Game::class)->findBy(['id' => 5]);

        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEmpty($deletedGame);
    }

    public function testDeleteGameResourceNotFound(): void
    {
        GameFactory::createMany(5);
        $this->client->jsonRequest(
            'DELETE',
            'https://localhost/api/games/6'
        );

        $response = $this->client->getResponse();
        $deletedGame = $this->manager->getRepository(Game::class)->findBy(['id' => 5]);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertNotEmpty($deletedGame);
    }

    public function testPatchGameSuccess(): void
    {
        GameFactory::createMany(5);
        $this->client->jsonRequest(
            'PATCH',
            'https://localhost/api/games/5',
            [
                'title' => 'Test_game'
            ],
            [
                'CONTENT_TYPE' => 'application/merge-patch+json',
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertEquals('Test_game', $decodedResponse['title']);
    }

    public function testPatchGameInvalidInput(): void
    {
        GameFactory::createMany(5);
        $this->client->jsonRequest(
            'PATCH',
            'https://localhost/api/games/5',
            [
                "title" => 123,
            ],
            [
                'CONTENT_TYPE' => 'application/merge-patch+json',
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertArrayHasKey('detail', $decodedResponse);
        $this->assertEquals(
            'The type of the "title" attribute must be "string", "integer" given.',
            $decodedResponse['detail']
        );
    }

    public function testPatchGameResourceNotFound(): void
    {
        GameFactory::createMany(5);
        $this->client->jsonRequest(
            'PATCH',
            'https://localhost/api/games/6',
            [
                'title' => 'Test_game'
            ],
            [
                'CONTENT_TYPE' => 'application/merge-patch+json',
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertEquals('Not Found', $decodedResponse['detail']);
    }

    public function testPatchNewUnprocessableEntity(): void
    {
        $this->authorize();
        $this->client->jsonRequest(
            'POST',
            'https://localhost/api/games',
            [
                'title' => 'Game0',
                'description' => 'test_description',
                'price' => 99,
                'purchasedGames' => []
            ]
        );
        $this->client->jsonRequest(
            'POST',
            'https://localhost/api/games',
            [
                'title' => 'Game1',
                'description' => 'test_description',
                'price' => 99,
                'purchasedGames' => []
            ]
        );

        $this->client->jsonRequest(
            'PATCH',
            'https://localhost/api/games/2',
            [
                'title' => 'Game0',
            ],
            [
                'CONTENT_TYPE' => 'application/merge-patch+json',
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertArrayHasKey('violations', $decodedResponse);
        $this->assertArrayHasKey('message', $decodedResponse['violations'][0]);
        $this->assertEquals(
            'Game with this title already exists',
            $decodedResponse['violations'][0]['message']
        );
    }
}
