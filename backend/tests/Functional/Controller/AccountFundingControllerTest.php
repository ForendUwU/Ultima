<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use App\Factory\UserFactory;
use App\Service\AccountFundingService;
use App\Service\TokenService;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\ResetDatabase;

class AccountFundingControllerTest extends WebTestCase
{
    use ResetDatabase;

    protected KernelBrowser $client;
    protected EntityManager $em;
    protected TokenService $tokenService;
    protected AccountFundingService $accountFundingService;
    public function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();

        $this->tokenService = $container->get(TokenService::class);
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
            'unauthorized ' => ['', true]
        ];
    }

    /**
     *  @dataProvider fundDataProvider
     */
    public function testFund($amount, $createFakeToken)
    {
        UserFactory::createOne([
            'login' => 'testLogin',
            'password' => 'testPassword1!',
            'balance' => '0.00'
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
            'PATCH',
            'https://localhost/api/fund',
            [
                'amount' => $amount
            ],
            [
                'HTTP_Authorization' => 'Bearer '.$testToken
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        if ($amount === '10' && !$createFakeToken) {
            $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
            $this->assertArrayHasKey('message', $decodedResponse);
            $this->assertNotEmpty($decodedResponse['message']);
            $this->assertEquals('Successfully funded', $decodedResponse['message']);

            $actualUser = $this->em->getRepository(User::class)->findOneBy(['login' => 'testLogin']);

            $this->assertEquals('10.00', $actualUser->getBalance());
        } else if ($amount === '' && !$createFakeToken) {
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