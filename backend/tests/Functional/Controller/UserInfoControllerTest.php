<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
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

    /**
     * @throws NotSupported
     * @throws ORMException
     */
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
}