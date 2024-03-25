<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use App\Service\TokenService;
use App\Tests\Traits\CreateUserTrait;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Zenstruck\Foundry\Test\ResetDatabase;
use Symfony\Component\HttpFoundation\Response;

class AuthorizationControllerTest extends WebTestCase
{
    use ResetDatabase, CreateUserTrait;

    protected KernelBrowser $client;
    protected EntityManager $em;
    protected static TokenService $tokenService;
    protected UserPasswordHasherInterface $passwordHasher;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();

        $this->passwordHasher = $container->get(UserPasswordHasherInterface::class);
        static::$tokenService = $container->get(TokenService::class);
        $this->em = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function loginDataProvider(): array
    {
        return [
            'success' => ['testLogin', 'testPassword'],
            'wrong password' => ['testLogin', 'wrongPassword'],
            'missing data' => ['', '']
        ];
    }

    public function registerDataProvider(): array
    {
        return [
            'success' => ['testLogin', 'testPassword1!', 'testEmail', 'testNickname'],
            'missing data' => ['', '', '', ''],
            'login already in use' => ['sameLogin', 'testPassword1!', 'testEmail', 'testNickname'],
        ];
    }

    /**
     *  @dataProvider loginDataProvider
     */
    public function testLogin($testLogin, $testPassword)
    {

        $testUser = $this->createUser();
        $testPasswordHashed= $this->passwordHasher->hashPassword($testUser, 'testPassword');
        $testUser->setPassword($testPasswordHashed);
        $this->em->persist($testUser);
        $this->em->flush();

        $this->client->jsonRequest(
            'POST',
            'https://localhost/api/login',
            [
                'login' => $testLogin,
                'password' => $testPassword
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        if ($testLogin && $testPassword === 'testPassword') {
            $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
            $this->assertArrayHasKey('token', $decodedResponse);
            $this->assertNotEmpty($decodedResponse['token']);
        } else if ($testLogin && $testPassword === 'wrongPassword') {
            $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
            $this->assertArrayHasKey('message', $decodedResponse);
            $this->assertNotEmpty($decodedResponse['message']);
            $this->assertEquals('Wrong login or password', $decodedResponse['message']);
        } else {
            $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
            $this->assertNotNull($decodedResponse['message']);
            $this->assertEquals('Missing data', $decodedResponse['message']);
        }
    }

    public function testLogout()
    {
        $testUser = $this->createUser();

        $this->em->persist($testUser);
        $this->em->flush();

        $testUser = $this->em->getRepository(User::class)->findOneBy(['login' => 'testLogin']);

        $testToken = $this->addTokenToUser($testUser);

        $this->em->persist($testUser);
        $this->em->flush();

        $this->client->jsonRequest(
            'POST',
            'https://localhost/api/logout',
            [
                'userId' => $testUser->getId()
            ],
            [
                'HTTP_Authorization' => 'Bearer '.$testToken
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertNotNull($decodedResponse['message']);
        $this->assertEquals('Logout successfully', $decodedResponse['message']);
    }

    /**
     *  @dataProvider registerDataProvider
     */
    public function testRegister($testLogin, $testPassword, $testEmail, $testNickname)
    {
        if ($testLogin === 'sameLogin') {
            $testUser = $this->createUser(
                login: 'sameLogin',
                nickname: 'someNickname',
                email: 'someemail@mail.com',
                password: 'somePassword123!'
            );
            $this->em->persist($testUser);
            $this->em->flush();
        }

        $this->client->jsonRequest(
            'POST',
            'https://localhost/api/register',
            [
                'login' => $testLogin,
                'password' => $testPassword,
                'email' => $testEmail,
                'nickname' => $testNickname
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        if ($testLogin === 'testLogin' && $testPassword && $testEmail && $testNickname) {
            $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
            $this->assertNotNull($decodedResponse['token']);

            $decodedToken = static::$tokenService->decode($decodedResponse['token']);

            $this->assertEquals($testLogin, $decodedToken->login);
            $this->assertEquals($testEmail, $decodedToken->email);
        } else if ($testLogin === 'sameLogin' && $testPassword && $testEmail && $testNickname) {
            $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
            $this->assertNotNull($decodedResponse['message']);

            $this->assertEquals('This login is already in use', $decodedResponse['message']);
        } else {
            $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
            $this->assertNotNull($decodedResponse['message']);

            $this->assertEquals('Missing data', $decodedResponse['message']);
        }
    }
}