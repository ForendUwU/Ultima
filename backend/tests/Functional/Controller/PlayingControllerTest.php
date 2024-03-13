<?php

namespace App\Tests\Functional\Controller;

use App\Entity\PurchasedGame;
use App\Entity\User;
use App\Factory\GameFactory;
use App\Factory\PurchasedGameFactory;
use App\Factory\UserFactory;
use App\Service\PlayingService;
use App\Service\TokenService;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\ResetDatabase;

class PlayingControllerTest extends WebTestCase
{
    use ResetDatabase;

    protected KernelBrowser $client;
    protected EntityManager $em;
    protected TokenService $tokenService;
    protected PlayingService $playingService;
    public function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();

        $this->tokenService = $container->get(TokenService::class);
        $this->playingService = $container->get(PlayingService::class);
        $this->em = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function savePlayingTimeDataProvider(): array
    {
        return [
            'success' => ['1', '3600000', false],
            'missing data' => ['', '', false],
            'unauthorized ' => ['', '', true]
        ];
    }

    /**
     *  @dataProvider savePlayingTimeDataProvider
     */
    public function testSavePlayingTime($gameId, $time, $createFakeToken)
    {
        $user = UserFactory::createOne([
            'login' => 'testLogin',
            'password' => 'testPassword1!',
            'nickname' => 'testNickname'
        ]);
        $game = GameFactory::createOne([
            'title' => 'testTitle'
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
            'POST',
            'https://localhost/api/save-playing-time',
            [
                'gameId' => $gameId,
                'time' => $time
            ],
            [
                'HTTP_Authorization' => 'Bearer '.$testToken
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        if ($time && $gameId && !$createFakeToken) {
            $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
            $this->assertArrayHasKey('message', $decodedResponse);
            $this->assertNotEmpty($decodedResponse['message']);
            $this->assertEquals('Successfully updated', $decodedResponse['message']);

            $actualPurchasedGame = $this->em
                ->getRepository(PurchasedGame::class)
                ->findOneBy(['user' => $testUser]);

            $this->assertEquals($time / 3600000, $actualPurchasedGame->getHoursOfPlaying());
        } else if (!$time && !$gameId && !$createFakeToken) {
            $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
            $this->assertArrayHasKey('message', $decodedResponse);
            $this->assertNotEmpty($decodedResponse['message']);
            $this->assertEquals('Missing data', $decodedResponse['message']);
        } else if ($createFakeToken) {
            $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
            $this->assertArrayHasKey('message', $decodedResponse);
            $this->assertNotEmpty($decodedResponse['message']);
            $this->assertEquals('Unauthorized', $decodedResponse['message']);
        }
    }
}