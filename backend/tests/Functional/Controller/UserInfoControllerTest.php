<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use App\Service\TokenService;
use App\Tests\Traits\CreateGameTrait;
use App\Tests\Traits\CreatePurchasedGameTrait;
use App\Tests\Traits\CreateUserTrait;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;
use Symfony\Component\HttpFoundation\Response;

class UserInfoControllerTest extends WebTestCase
{
    use ResetDatabase, CreateUserTrait, CreateGameTrait, CreatePurchasedGameTrait;

    protected KernelBrowser $client;
    protected EntityManager $em;
    protected static TokenService $tokenService;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();

        static::$tokenService = $container->get(TokenService::class);
        $this->em = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function getUsersMostPlayedGamesDataProvider(): array
    {
        return [
            'success' => [false],
            'unauthorized' => [true]
        ];
    }

    public function testGetUserInfo()
    {
        $testUser = $this->createUser();
        $this->em->persist($testUser);
        $this->em->flush();

        $testToken = $this->addTokenToUser($testUser);
        $this->em->flush();

        $this->client->jsonRequest(
            'GET',
            'https://localhost/api/user/me',
            [],
            [
                'HTTP_Authorization' => 'Bearer '.$testToken
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNotEmpty($decodedResponse);
        $this->assertNotNull($decodedResponse['login']);
        $this->assertNotNull($decodedResponse['nickname']);
        $this->assertNotNull($decodedResponse['balance']);
        $this->assertNotNull($decodedResponse['firstName']);
        $this->assertNotNull($decodedResponse['lastName']);
        $this->assertNotNull($decodedResponse['email']);
        $this->assertEquals($testUser->getLogin(), $decodedResponse['login']);
        $this->assertEquals($testUser->getNickname(), $decodedResponse['nickname']);
        $this->assertEquals($testUser->getBalance(), $decodedResponse['balance']);
        $this->assertEquals($testUser->getFirstName(), $decodedResponse['firstName']);
        $this->assertEquals($testUser->getLastName(), $decodedResponse['lastName']);
        $this->assertEquals($testUser->getEmail(), $decodedResponse['email']);
        $this->assertArrayHasKey('purchasedGames', $decodedResponse);
    }

    /**
     *  @dataProvider getUsersMostPlayedGamesDataProvider
     */
    public function testGetUsersMostPlayedGames($createFakeToken)
    {
        $testUser = $this->createUser();
        $this->em->persist($testUser);
        $this->em->flush();
        $testToken = $this->addTokenToUser($testUser);

        $testGame1 = $this->createGame(
            title: 'testTitle1'
        );

        $testGame2 = $this->createGame(
            title: 'testTitle2'
        );

        $testPurchasedGame1 = $this->createPurchasedGame(
            user: $testUser,
            game: $testGame1,
            hoursOfPlaying: 1
        );

        $testPurchasedGame2 = $this->createPurchasedGame(
            user: $testUser,
            game: $testGame2,
            hoursOfPlaying: 2
        );

        $this->em->persist($testGame1);
        $this->em->persist($testGame2);
        $this->em->persist($testPurchasedGame1);
        $this->em->persist($testPurchasedGame2);

        if ($createFakeToken) {
            $testUser->setToken(null);
        }

        $this->em->flush();

        $this->client->jsonRequest(
            'GET',
            'https://localhost/api/user/most-played-games',
            [],
            [
                'HTTP_Authorization' => 'Bearer '.$testToken
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        if (!$createFakeToken) {
            $this->assertNotEmpty($decodedResponse);
            $this->assertArrayHasKey(0, $decodedResponse);
            $this->assertArrayHasKey(1, $decodedResponse);
            $this->assertNotEmpty($decodedResponse[0]);
            $this->assertNotEmpty($decodedResponse[1]);
            $this->assertArrayHasKey('title', $decodedResponse[0]);
            $this->assertArrayHasKey('hoursOfPlaying', $decodedResponse[0]);
            $this->assertArrayHasKey('title', $decodedResponse[1]);
            $this->assertArrayHasKey('hoursOfPlaying', $decodedResponse[1]);
            $this->assertEquals('testTitle2', $decodedResponse[0]['title']);
            $this->assertEquals(2, $decodedResponse[0]['hoursOfPlaying']);
            $this->assertEquals('testTitle1', $decodedResponse[1]['title']);
            $this->assertEquals(1, $decodedResponse[1]['hoursOfPlaying']);
        } else if ($createFakeToken) {
            $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
            $this->assertArrayHasKey('message', $decodedResponse);
            $this->assertNotEmpty($decodedResponse['message']);
            $this->assertEquals('Unauthorized', $decodedResponse['message']);
        }
    }

    public function testUpdateUserInfo()
    {
        $testUser = $this->createUser();
        $this->em->persist($testUser);
        $this->em->flush();
        $testToken = $this->addTokenToUser($testUser);
        $this->em->flush();

        $this->client->jsonRequest(
            'POST',
            'https://localhost/api/user/change-data',
            [
                'nickname' => 'newNickname',
                'firstName' => '',
                'lastName' => '',
                'email' => ''
            ],
            [
                'HTTP_Authorization' => 'Bearer '.$testToken
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertNotNull($decodedResponse);
        $this->assertArrayHasKey('result', $decodedResponse);
        $this->assertNotNull($decodedResponse['result']);
        $this->assertEquals('Successfully updated', $decodedResponse['result']);

        $updatedUser = $this->em->getRepository(User::class)->findById($testUser->getId());

        $this->assertEquals('newNickname', $updatedUser->getNickname());
        $this->assertEquals($testUser->getFirstName(), $updatedUser->getFirstName());
        $this->assertEquals($testUser->getLastName(), $updatedUser->getLastName());
        $this->assertEquals($testUser->getEmail(), $updatedUser->getEmail());
    }
}