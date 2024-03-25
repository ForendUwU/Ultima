<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use App\Service\AccountFundingService;
use App\Service\TokenService;
use App\Tests\Traits\CreateUserTrait;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\ResetDatabase;

class AccountFundingControllerTest extends WebTestCase
{
    use ResetDatabase, CreateUserTrait;

    protected KernelBrowser $client;
    protected EntityManager $em;
    protected static TokenService $tokenService;
    protected AccountFundingService $accountFundingService;
    public function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();

        static::$tokenService = $container->get(TokenService::class);
        $this->accountFundingService = $container->get(AccountFundingService::class);
        $this->em = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function fundDataProvider(): array
    {
        return [
            'success' => ['10', false],
            'missing data' => ['', false],
            'unauthorized ' => ['10', true]
        ];
    }

    /**
     *  @dataProvider fundDataProvider
     */
    public function testFund($amount, $unauthorizedUser)
    {
        $testUser = $this->createUser();

        $this->em->persist($testUser);
        $this->em->flush();

        $testUser = $this->em->getRepository(User::class)->findByLogin('testLogin');

        $testToken = $this->addTokenToUser($testUser);

        if ($unauthorizedUser) {
            $testUser->setToken(null);
        }

        $this->em->flush();

        $this->client->jsonRequest(
            'POST',
            'https://localhost/api/user/fund',
            [
                'amount' => $amount
            ],
            [
                'HTTP_Authorization' => 'Bearer '.$testToken
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        if ($amount === '10' && !$unauthorizedUser) {
            $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
            $this->assertArrayHasKey('newAmount', $decodedResponse);
            $this->assertNotEmpty($decodedResponse['newAmount']);
            $this->assertEquals('10.00', $decodedResponse['newAmount']);

            $actualUser = $this->em->getRepository(User::class)->findOneBy(['login' => 'testLogin']);

            $this->assertEquals('10.00', $actualUser->getBalance());
        } else if ($amount === '' && !$unauthorizedUser) {
            $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
            $this->assertArrayHasKey('message', $decodedResponse);
            $this->assertNotEmpty($decodedResponse['message']);
            $this->assertEquals('Missing data', $decodedResponse['message']);
        } else if ($unauthorizedUser) {
            $this->assertEquals(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
            $this->assertArrayHasKey('message', $decodedResponse);
            $this->assertNotEmpty($decodedResponse['message']);
            $this->assertEquals('Unauthorized', $decodedResponse['message']);
        }
    }
}