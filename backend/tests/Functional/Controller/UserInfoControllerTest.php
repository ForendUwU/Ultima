<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use App\Factory\UserFactory;
use App\Services\TokenService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
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

        $this->client->jsonRequest(
            'GET',
            'https://localhost/api/user-info-by-token',
            [],
            [
                'HTTP_Authorization' => $testToken
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNotNull($decodedResponse);
        $this->assertEquals($testUser->getLogin(), $decodedResponse['login']);
        $this->assertEquals($testUser->getNickname(), $decodedResponse['nickname']);
        $this->assertEquals($testUser->getBalance(), $decodedResponse['balance']);
        $this->assertEquals($testUser->getFirstName(), $decodedResponse['firstName']);
        $this->assertEquals($testUser->getLastName(), $decodedResponse['lastName']);
        $this->assertEquals($testUser->getEmail(), $decodedResponse['email']);
        $this->assertEmpty($decodedResponse['purchasedGames']);
    }

    public function testGetUserInfoTokenIsMissing()
    {
        $this->client->jsonRequest(
            'GET',
            'https://localhost/api/user-info-by-token',
            [],
            [
                'HTTP_Authorization' => ''
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertNotEmpty($decodedResponse['message']);
        $this->assertEquals('Token is missing', $decodedResponse['message']);
    }
}