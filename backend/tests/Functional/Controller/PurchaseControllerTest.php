<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Game;
use App\Entity\PurchasedGame;
use App\Entity\User;
use App\Factory\GameFactory;
use App\Factory\PurchasedGameFactory;
use App\Factory\UserFactory;
use App\Service\TokenService;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;
use Symfony\Component\HttpFoundation\Response;

class PurchaseControllerTest extends WebTestCase
{
    use ResetDatabase;

    protected KernelBrowser $client;
    protected EntityManager $em;
    protected TokenService $tokenService;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();

        $this->tokenService = $container->get(TokenService::class);
        $this->em = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function purchaseDataProvider()
    {
        return [
           'success' => [1],
           'missing data' => [null],
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
        UserFactory::createOne([
            'login' => 'testLogin',
            'password' => 'testPassword',
            'balance' => '999'
        ]);

        GameFactory::createOne([
            'title' => 'testTitle1',
            'price' => '99'
        ]);

        GameFactory::createOne([
            'title' => 'testTitle2',
            'price' => '9999'
        ]);

        $testUser = $this->em->getRepository(User::class)->findOneBy(['login' => 'testLogin']);
        $testToken = $this->tokenService->createToken($testUser);

        $testUser->setToken($testToken);

        $this->em->persist($testUser);
        $this->em->flush();

        $this->client->jsonRequest(
            'POST',
            'https://localhost/api/purchase-game',
            [
                'gameId' => $gameId
            ],
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
        } else if (!$gameId) {
            $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
            $this->assertNotEmpty($decodedResponse['message']);
            $this->assertEquals('Missing data', $decodedResponse['message']);
        }
    }

    public function testGetPurchasedGamesSuccess()
    {
        UserFactory::createOne([
            'login' => 'testLogin',
            'password' => 'testPassword'
        ]);

        GameFactory::createOne([
            'title' => 'testTitle'
        ]);

        $testUser = $this->em->getRepository(User::class)->findOneBy(['login' => 'testLogin']);
        $testGame = $this->em->getRepository(Game::class)->findOneBy(['title' => 'testTitle']);

        PurchasedGameFactory::createOne([
            'user' => $testUser,
            'game' => $testGame
        ]);

        $testToken = $this->tokenService->createToken($testUser);

        $testUser->setToken($testToken);

        $this->em->persist($testUser);
        $this->em->flush();

        $this->client->jsonRequest(
            'GET',
            'https://localhost/api/purchase-game',
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
    public function testDeletePurchasedGame1($gameId, $createFakeToken)
    {
        $user = UserFactory::createOne([
            'login' => 'testLogin',
            'password' => 'testPassword',
            'balance' => '999'
        ]);

        $game = GameFactory::createOne([
            'title' => 'testTitle1',
            'price' => '99'
        ]);

        PurchasedGameFactory::createOne([
            'game' => $game,
            'user' => $user,
            'hoursOfPlaying' => '0'
        ]);

        $testUser = $this->em->getRepository(User::class)->findOneBy(['login' => 'testLogin']);

        $testToken = $this->tokenService->createToken($testUser);

        $testUser->setToken($testToken);

        $this->em->persist($testUser);
        $this->em->flush();

        if ($createFakeToken) {
            $fakeUser = new User();
            $fakeUser->setLogin('fakeLogin');

            $testToken = $this->tokenService->createToken($fakeUser);
        }

        $this->client->jsonRequest(
            'DELETE',
            'https://localhost/api/purchase-game',
            [
                'gameId' => $gameId
            ],
            [
                'HTTP_Authorization' => 'Bearer '.$testToken
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        if ($gameId && !$createFakeToken) {
            $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
            $this->assertNotEmpty($decodedResponse['message']);
            $this->assertEquals('Successfully deleted', $decodedResponse['message']);

            $purchasedGame = $this->em->getRepository(PurchasedGame::class)->findOneBy(['user' => $testUser]);

            $this->assertNull($purchasedGame);
        } else if (!$gameId && !$createFakeToken) {
            $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
            $this->assertNotEmpty($decodedResponse['message']);
            $this->assertEquals('Missing data', $decodedResponse['message']);
        } else if (!$gameId && $createFakeToken) {
            $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
            $this->assertNotEmpty($decodedResponse['message']);
            $this->assertEquals('Unauthorized', $decodedResponse['message']);
        }
    }
}