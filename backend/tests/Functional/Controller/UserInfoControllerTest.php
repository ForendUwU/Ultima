<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use App\Factory\GameFactory;
use App\Factory\PurchasedGameFactory;
use App\Factory\UserFactory;
use App\Service\TokenService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;
use Symfony\Component\HttpFoundation\Response;

class UserInfoControllerTest extends WebTestCase
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

    public function getUsersMostPlayedGamesDataProvider(): array
    {
        return [
            'success' => [false],
            'unauthorized' => [true]
        ];
    }

    public function testGetUserInfoSuccess()
    {
        UserFactory::createOne([
            'login' => 'testLogin',
            'password' => 'testPassword'
        ]);

        $testUser = $this->em->getRepository(User::class)->findOneBy(['login' => 'testLogin']);
        $testToken = $this->tokenService->createToken($testUser);

        $testUser->setToken($testToken);

        $this->em->persist($testUser);
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
    public function testGetUsersMostPlayedGames1($createFakeToken)
    {
        $testUser = UserFactory::createOne([
            'login' => 'testLogin',
            'password' => 'testPassword'
        ]);

        $testGame1 = GameFactory::createOne([
            'title' => 'testTitle1'
        ]);

        $testGame2 = GameFactory::createOne([
            'title' => 'testTitle2'
        ]);

        PurchasedGameFactory::createOne([
            'user' => $testUser,
            'game' => $testGame1,
            'hoursOfPlaying' => 1
        ]);

        PurchasedGameFactory::createOne([
            'user' => $testUser,
            'game' => $testGame2,
            'hoursOfPlaying' => 2
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
            'GET',
            'https://localhost/api/user/get-most-played-games',
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
}