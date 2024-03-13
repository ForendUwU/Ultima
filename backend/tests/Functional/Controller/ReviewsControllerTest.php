<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Game;
use App\Entity\Review;
use App\Entity\User;
use App\Factory\GameFactory;
use App\Factory\ReviewFactory;
use App\Factory\UserFactory;
use App\Service\TokenService;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;
use Symfony\Component\HttpFoundation\Response;

class ReviewsControllerTest extends WebTestCase
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

    public function createGameReviewDataProvider(): array
    {
        return [
            'success' => ['test content', false],
            'missing data' => [null, false],
            'user already has review' => ['test content', true]
        ];
    }

    public function getGameReviewsDataProvider(): array
    {
        return [
            'with review creating' => [true],
            'without review creating' => [false]
        ];
    }

    public function changeGameReviewDataProvider(): array
    {
        return [
            'success' => ['test content after change', true],
            'missing data' => [null, true],
            'user doesn\'t have a review' => ['test content', false]
        ];
    }

    public function deleteReviewDataProvider(): array
    {
        return [
            'success' => [true],
            'user doesn\'t have a review' => [false]
        ];
    }

    public function getUserReviewContentByGameIdDataProvider(): array
    {
        return [
            'success' => [true],
            'user doesn\'t have a review' => [false]
        ];
    }

    /**
     *  @dataProvider createGameReviewDataProvider
     */
    public function testCreateGameReview($reviewContent, $createFakeReview)
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

        $testUser = $this->em->getRepository(User::class)->findOneBy(['login' => 'testLogin']);
        $testToken = $this->tokenService->createToken($testUser);

        $testUser->setToken($testToken);

        $this->em->persist($testUser);
        $this->em->flush();

        $testGame = $this->em->getRepository(Game::class)->findOneBy(['title' => 'testTitle1']);

        if ($createFakeReview) {
            ReviewFactory::createOne([
                'user' => $testUser,
                'game' => $testGame
            ]);
        }

        $this->client->jsonRequest(
            'POST',
            'https://localhost/api/reviews/1',
            [
                'content' => $reviewContent
            ],
            [
                'HTTP_Authorization' => 'Bearer '.$testToken
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        if ($reviewContent != null && !$createFakeReview) {
            $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
            $this->assertNotEmpty($decodedResponse['message']);
            $this->assertEquals('Review was created successfully', $decodedResponse['message']);

            $review = $this->em->getRepository(Review::class)->findOneBy(['user' => $testUser, 'game' => $testGame]);

            $this->assertNotNull($review);
            $this->assertNotNull($review->getGame());
            $this->assertNotNull($review->getUser());
            $this->assertNotNull($review->getContent());
            $this->assertEquals($testGame, $review->getGame());
            $this->assertEquals($testUser, $review->getUser());
            $this->assertEquals($reviewContent, $review->getContent());
        } else if ($reviewContent != null && $createFakeReview) {
            $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
            $this->assertNotEmpty($decodedResponse['message']);
            $this->assertEquals('User already has review on this game', $decodedResponse['message']);
        } else if ($reviewContent == null && !$createFakeReview) {
            $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
            $this->assertNotEmpty($decodedResponse['message']);
            $this->assertEquals('Missing data', $decodedResponse['message']);
        }
    }

    /**
     *  @dataProvider getGameReviewsDataProvider
     */
    public function testGetGameReviews($createFakeReview)
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

        if ($createFakeReview) {
            ReviewFactory::createOne([
                'user' => $testUser,
                'game' => $testGame,
                'likes' => 0,
                'dislikes' => 0
            ]);
        }

        $testToken = $this->tokenService->createToken($testUser);

        $testUser->setToken($testToken);

        $this->em->persist($testUser);
        $this->em->flush();

        $this->client->jsonRequest(
            'GET',
            'https://localhost/api/reviews/1'
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        if ($createFakeReview) {
            $this->assertNotEmpty($decodedResponse);
            $this->assertArrayHasKey(0, $decodedResponse);
            $this->assertNotEmpty($decodedResponse[0]);
            $this->assertArrayHasKey('content', $decodedResponse[0]);
            $this->assertArrayHasKey('likes', $decodedResponse[0]);
            $this->assertArrayHasKey('dislikes', $decodedResponse[0]);
            $this->assertArrayHasKey('userNickname', $decodedResponse[0]);
            $this->assertNotNull($decodedResponse[0]['content']);
            $this->assertEquals(0, $decodedResponse[0]['likes']);
            $this->assertEquals(0, $decodedResponse[0]['dislikes']);
            $this->assertEquals($testUser->getNickname(), $decodedResponse[0]['userNickname']);
        } else if (!$createFakeReview) {
            $this->assertEmpty($decodedResponse);
        }
    }

    /**
     *  @dataProvider changeGameReviewDataProvider
     */
    public function testChangeGameReviewContent($reviewContent, $createFakeReview)
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

        $testGame = $this->em->getRepository(Game::class)->findOneBy(['title' => 'testTitle1']);
        $testUser = $this->em->getRepository(User::class)->findOneBy(['login' => 'testLogin']);
        $testToken = $this->tokenService->createToken($testUser);

        $testUser->setToken($testToken);

        $this->em->persist($testUser);
        $this->em->flush();

        if ($createFakeReview) {
            ReviewFactory::createOne([
                'user' => $testUser,
                'game' => $testGame,
                'content' => 'content before change'
            ]);
        }

        $this->client->jsonRequest(
            'PATCH',
            'https://localhost/api/reviews/1',
            [
                'content' => $reviewContent
            ],
            [
                'HTTP_Authorization' => 'Bearer '.$testToken
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        if ($reviewContent != null && $createFakeReview) {
            $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
            $this->assertNotEmpty($decodedResponse['message']);
            $this->assertEquals('Review was successfully changed', $decodedResponse['message']);

            $review = $this->em->getRepository(Review::class)->findOneBy(['user' => $testUser, 'game' => $testGame]);

            $this->assertNotNull($review);
            $this->assertNotNull($review->getGame());
            $this->assertNotNull($review->getUser());
            $this->assertNotNull($review->getContent());
            $this->assertEquals($testGame, $review->getGame());
            $this->assertEquals($testUser, $review->getUser());
            $this->assertEquals($reviewContent, $review->getContent());
        } else if ($reviewContent != null && !$createFakeReview) {
            $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
            $this->assertNotEmpty($decodedResponse['message']);
            $this->assertEquals('User\'s review not found', $decodedResponse['message']);
        } else if ($reviewContent == null && $createFakeReview) {
            $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
            $this->assertNotEmpty($decodedResponse['message']);
            $this->assertEquals('Missing data', $decodedResponse['message']);
        }
    }

    /**
     *  @dataProvider deleteReviewDataProvider
     */
    public function testDeleteReview($createFakeReview)
    {
        $user = UserFactory::createOne([
            'login' => 'testLogin',
            'password' => 'testPassword',
        ]);

        $game = GameFactory::createOne([
            'title' => 'testTitle1',
            'price' => '99'
        ]);

        $testGame = $this->em->getRepository(Game::class)->findOneBy(['title' => 'testTitle1']);
        $testUser = $this->em->getRepository(User::class)->findOneBy(['login' => 'testLogin']);

        $testToken = $this->tokenService->createToken($testUser);

        $testUser->setToken($testToken);

        $this->em->persist($testUser);
        $this->em->flush();

        if ($createFakeReview) {
            ReviewFactory::createOne([
                'game' => $game,
                'user' => $user
            ]);
        }

        $this->client->jsonRequest(
            'DELETE',
            'https://localhost/api/reviews/1',
            [],
            [
                'HTTP_Authorization' => 'Bearer '.$testToken
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        if ($createFakeReview) {
            $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
            $this->assertNotEmpty($decodedResponse['message']);
            $this->assertEquals('Review was successfully deleted', $decodedResponse['message']);

            $purchasedGame = $this->em->getRepository(Review::class)->findOneBy(['user' => $testUser, 'game' => $testGame]);

            $this->assertNull($purchasedGame);
        } else if (!$createFakeReview) {
            $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
            $this->assertNotEmpty($decodedResponse['message']);
            $this->assertEquals('User\'s review not found', $decodedResponse['message']);
        }
    }

    /**
     *  @dataProvider getUserReviewContentByGameIdDataProvider
     */
    public function testGetUserReviewContentByGameId($createFakeReview)
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

        if ($createFakeReview) {
            ReviewFactory::createOne([
                'user' => $testUser,
                'game' => $testGame,
                'likes' => 0,
                'dislikes' => 0
            ]);
        }

        $testToken = $this->tokenService->createToken($testUser);

        $testUser->setToken($testToken);

        $this->em->persist($testUser);
        $this->em->flush();

        $this->client->jsonRequest(
            'GET',
            'https://localhost/api/user/review/1',
            [],
            [
                'HTTP_Authorization' => 'Bearer '.$testToken
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        if ($createFakeReview) {
            $this->assertNotEmpty($decodedResponse);
            $this->assertArrayHasKey('message', $decodedResponse);
            $this->assertNotNull($decodedResponse['message']);
        } else if (!$createFakeReview) {
            $this->assertNotEmpty($decodedResponse);
            $this->assertArrayHasKey('message', $decodedResponse);
            $this->assertEmpty($decodedResponse['message']);
        }
    }
}