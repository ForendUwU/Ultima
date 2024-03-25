<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Game;
use App\Entity\PurchasedGame;
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

class PurchaseControllerTest extends WebTestCase
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

    public function purchaseDataProvider()
    {
        return [
           'success' => [1],
           'not enough money' => [2]
        ];
    }

    public function deletePurchasedGameDataProvider()
    {
        return [
            'success' => [1, false],
            'missing data' => [null, false],
            'unauthorized' => [null, true]
        ];
    }

    /**
     *  @dataProvider purchaseDataProvider
     */
    public function testPurchase($gameId)
    {
        $testUser = $this->createUser(
            balance: 999
        );

        $this->em->persist($testUser);
        $this->em->flush();

        $testGame = $this->createGame(
            title: 'testTitle1',
            price: 99
        );

        $testGame2 = $this->createGame(
            title: 'testTitle2',
            price: 9999
        );

        $testUser = $this->em->getRepository(User::class)->findOneBy(['login' => 'testLogin']);

        $testToken = $this->addTokenToUser($testUser);

        $this->em->persist($testGame);
        $this->em->persist($testGame2);
        $this->em->flush();

        $this->client->jsonRequest(
            'POST',
            'https://localhost/api/game/'.$gameId.'/purchase',
            [],
            [
                'HTTP_Authorization' => 'Bearer '.$testToken
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        if ($gameId === 1) {
            $testGame = $this->em->getRepository(Game::class)->findOneBy(['title' => 'testTitle1']);

            $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
            $this->assertNotEmpty($decodedResponse['message']);
            $this->assertEquals('Successfully purchased', $decodedResponse['message']);

            $purchasedGame = $this->em->getRepository(PurchasedGame::class)->findOneBy(['user' => $testUser]);

            $this->assertNotNull($purchasedGame);
            $this->assertNotNull($purchasedGame->getGame());
            $this->assertEquals($testGame->getId(), $purchasedGame->getGame()->getId());
            $this->assertEquals($testGame->getTitle(), $purchasedGame->getGame()->getTitle());
            $this->assertEquals(0, $purchasedGame->getHoursOfPlaying());
        } else if ($gameId === 2) {
            $this->assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
            $this->assertNotEmpty($decodedResponse['message']);
            $this->assertEquals('Not enough money', $decodedResponse['message']);
        }
    }

    public function testGetPurchasedGamesSuccess()
    {
        $testUser = $this->createUser();

        $this->em->persist($testUser);
        $this->em->flush();

        $testGame = $this->createGame();
        $testPurchasedGame = $this->createPurchasedGame(
            user: $testUser,
            game: $testGame
        );

        $testUser = $this->em->getRepository(User::class)->findOneByLogin($testUser->getLogin());
        $testToken = $this->addTokenToUser($testUser);

        $testUser->addPurchasedGame($testPurchasedGame);

        $this->em->persist($testGame);
        $this->em->persist($testPurchasedGame);
        $this->em->flush();

        $this->client->jsonRequest(
            'GET',
            'https://localhost/api/user/purchased-games',
            [],
            [
                'HTTP_Authorization' => 'Bearer '.$testToken
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        $this->assertNotEmpty($decodedResponse);
        $this->assertArrayHasKey(0, $decodedResponse);
        $this->assertNotEmpty($decodedResponse[0]);
        $this->assertNotNull($decodedResponse[0]['gameId']);
        $this->assertNotNull($decodedResponse[0]['title']);
        $this->assertNotNull($decodedResponse[0]['hoursOfPlaying']);

        $purchasedGame = $this->em->getRepository(PurchasedGame::class)->findOneBy(['user' => $testUser]);

        $this->assertEquals($purchasedGame->getGame()->getTitle(), $decodedResponse[0]['title']);
        $this->assertEquals($purchasedGame->getGame()->getId(), $decodedResponse[0]['gameId']);
        $this->assertEquals($purchasedGame->getHoursOfPlaying(), $decodedResponse[0]['hoursOfPlaying']);
    }

    /**
     *  @dataProvider deletePurchasedGameDataProvider
     */
    public function testDeletePurchasedGame($purchasedGameId, $createFakeToken)
    {
        $testUser = $this->createUser();

        $this->em->persist($testUser);
        $this->em->flush();

        $testGame = $this->createGame();
        $testPurchasedGame = $this->createPurchasedGame(
            user: $testUser,
            game: $testGame
        );

        $testUser = $this->em->getRepository(User::class)->findOneBy(['login' => 'testLogin']);

        $testToken = $this->addTokenToUser($testUser);

        $this->em->persist($testGame);
        $this->em->persist($testPurchasedGame);

        if ($createFakeToken) {
            $testUser->setToken(null);
        }

        $this->em->flush();

        $this->client->jsonRequest(
            'DELETE',
            'https://localhost/api/purchased-games',
            [
                'purchasedGameId' => $purchasedGameId
            ],
            [
                'HTTP_Authorization' => 'Bearer '.$testToken
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        if ($purchasedGameId && !$createFakeToken) {
            $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
            $this->assertNotEmpty($decodedResponse['message']);
            $this->assertEquals('Successfully deleted', $decodedResponse['message']);

            $purchasedGame = $this->em->getRepository(PurchasedGame::class)->findOneBy(['user' => $testUser]);

            $this->assertNull($purchasedGame);
        } else if (!$purchasedGameId && !$createFakeToken) {
            $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
            $this->assertNotEmpty($decodedResponse['message']);
            $this->assertEquals('Missing data', $decodedResponse['message']);
        } else if (!$purchasedGameId && $createFakeToken) {
            $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
            $this->assertNotEmpty($decodedResponse['message']);
            $this->assertEquals('Unauthorized', $decodedResponse['message']);
        }
    }
}