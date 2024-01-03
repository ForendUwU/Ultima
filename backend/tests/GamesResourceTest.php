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
    //use ResetDatabase;

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
        $user = UserFactory::createOne([
            'login' => 'test_login',
            'password' => 'pass'
        ]);

        $this->client->jsonRequest(
            'POST',
            'https://localhost/login',
            [
                'login' => $user->getLogin(),
                'password' => $user->getPassword()
            ]
        );
    }

    public function testGetGamesCollection(): void
    {
        GameFactory::createMany(5);
        $this->client->request(
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

    public function testCreateNewGameSuccess(): void
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

    public function testCreateNewGameUnauthorized(): void
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

    public function testCreateNewGameInvalidInput(): void
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

        //dump($decodedResponse);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertArrayHasKey('detail', $decodedResponse);
        $this->assertEquals(
            'The type of the "title" attribute must be "string", "integer" given.',
            $decodedResponse['detail']
        );
    }

    public function testGetGame(): void
    {
        GameFactory::createMany(5);
        $gameId = $this->manager->getRepository(Game::class)->findAll()[0]->getId();
        $this->client->request(
            'GET',
            'https://localhost/api/games/'.$gameId
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
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
}
