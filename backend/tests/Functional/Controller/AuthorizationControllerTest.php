<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use App\Factory\UserFactory;
use App\Services\TokenService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;
use Symfony\Component\HttpFoundation\Response;

class AuthorizationControllerTest extends WebTestCase
{
    use ResetDatabase;

    protected $client;
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

    public function testLoginSuccess()
    {
        UserFactory::createOne([
            'login' => 'testLogin',
            'password' => 'testPassword'
        ]);

        $testUser = $this->em->getRepository(User::class)->findOneBy(['login' => 'testLogin']);


        $this->client->jsonRequest(
            'POST',
            'https://localhost/api/login',
            [
                'login' => 'testLogin',
                'password' => 'testPassword'
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNotEmpty($decodedResponse['token']);
    }

    public function testLoginMissingData()
    {
        UserFactory::createOne([
            'login' => 'testLogin',
            'password' => 'testPassword'
        ]);

        $this->client->jsonRequest(
            'POST',
            'https://localhost/api/login',
            [
                'login' => '',
                'password' => ''
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertNotNull($decodedResponse['message']);
        $this->assertEquals('Missing data', $decodedResponse['message']);
    }

    /**
     * @throws NotSupported
     */
    public function testLogoutSuccess()
    {
        UserFactory::createOne([
            'login' => 'testLogin',
            'password' => 'testPassword'
        ]);

        $testUser = $this->em->getRepository(User::class)->findOneBy(['login' => 'testLogin']);
        $testToken = $this->tokenService->createToken($testUser);

        $testUser->setToken($testToken);

        $this->client->jsonRequest(
            'POST',
            'https://localhost/api/logout',
            [],
            [
                'HTTP_Authorization' => $testToken
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNotNull($decodedResponse['message']);
        $this->assertEquals('Logout successfully', $decodedResponse['message']);
    }

    public function testLogoutMissingToken()
    {
        $this->client->jsonRequest(
            'POST',
            'https://localhost/api/logout'
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertNotNull($decodedResponse['message']);
        $this->assertEquals('Missing token', $decodedResponse['message']);
    }

    public function testRegisterSuccess()
    {
        UserFactory::createOne([
            'login' => 'testLogin',
            'password' => 'testPassword'
        ]);

        $testUser = $this->em->getRepository(User::class)->findOneBy(['login' => 'testLogin']);
        $testToken = $this->tokenService->createToken($testUser);

        $testUser->setToken($testToken);

        $this->client->jsonRequest(
            'POST',
            'https://localhost/api/logout',
            [],
            [
                'HTTP_Authorization' => $testToken
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNotNull($decodedResponse['message']);
        $this->assertEquals('Logout successfully', $decodedResponse['message']);
    }
}