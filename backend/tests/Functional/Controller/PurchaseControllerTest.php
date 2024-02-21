<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\Factory\GameFactory;
use App\Factory\PurchasedGameFactory;
use App\Factory\UserFactory;
use App\Service\TokenService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
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

    /**
     * @throws OptimisticLockException
     * @throws NotSupported
     * @throws ORMException
     */
    public function testPurchaseSuccess()
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

        $testToken = $this->tokenService->createToken($testUser);

        $testUser->setToken($testToken);

        $this->em->persist($testUser);
        $this->em->flush();

        $this->client->jsonRequest(
            'POST',
            'https://localhost/api/purchase-game',
            [
                'gameId' => $testGame->getId()
            ],
            [
                'HTTP_Authorization' => 'Bearer '.$testToken
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNotEmpty($decodedResponse['message']);
        $this->assertEquals('Successfully purchased', $decodedResponse['message']);
    }

    /**
     * @throws OptimisticLockException
     * @throws NotSupported
     * @throws ORMException
     */
    public function testPurchaseMissingData()
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
            'POST',
            'https://localhost/api/purchase-game',
            [
                'gameId' => ''
            ],
            [
                'HTTP_Authorization' => 'Bearer '.$testToken
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertNotEmpty($decodedResponse['message']);
        $this->assertEquals('Missing data', $decodedResponse['message']);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws NotSupported
     */
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
            'https://localhost/api/user/get-purchased-games',
            [],
            [
                'HTTP_Authorization' => 'Bearer '.$testToken
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNotEmpty($decodedResponse[0]);
        $this->assertNotNull($decodedResponse[0]['gameId']);
        $this->assertEquals('testTitle', $decodedResponse[0]['title']);
        $this->assertNotNull($decodedResponse[0]['hoursOfPlaying']);
    }
}