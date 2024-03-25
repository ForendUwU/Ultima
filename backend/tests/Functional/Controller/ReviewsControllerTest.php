<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Game;
use App\Entity\Review;
use App\Service\TokenService;
use App\Tests\Traits\CreateGameTrait;
use App\Tests\Traits\CreateReviewTrait;
use App\Tests\Traits\CreateUserTrait;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Foundry\Test\ResetDatabase;
use Symfony\Component\HttpFoundation\Response;

class ReviewsControllerTest extends WebTestCase
{
    use ResetDatabase, CreateUserTrait, CreateGameTrait, CreateReviewTrait;

    protected KernelBrowser $client;
    protected EntityManager $em;
    protected static TokenService $tokenService;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $container = static::getContainer();

        static::$tokenService = $container->get(TokenService::class);
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
        $testUser = $this->createUser();
        $this->em->persist($testUser);
        $this->em->flush();
        $testToken = $this->addTokenToUser($testUser);

        $testGame = $this->createGame();
        $this->em->persist($testGame);

        if ($createFakeReview) {
            $testReview = $this->createReview($testUser, $testGame);
            $this->em->persist($testReview);
        }

        $this->em->flush();

        $testGameId = $this->em->getRepository(Game::class)->findOneBy(['title' => 'testTitle'])->getId();

        $this->client->jsonRequest(
            'POST',
            'https://localhost/api/games/'.$testGameId.'/review',
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
        $testUser = $this->createUser();
        $testGame = $this->createGame();

        $this->addTokenToUser($testUser);

        $this->em->persist($testUser);
        $this->em->persist($testGame);

        if ($createFakeReview) {
            $testReview = $this->createReview($testUser, $testGame);
            $this->em->persist($testReview);
        }

        $this->em->flush();

        $this->client->jsonRequest(
            'GET',
            'https://localhost/api/games/'.$testGame->getId().'/reviews'
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());

        if ($createFakeReview) {
            $this->assertNotEmpty($decodedResponse);
            $this->assertArrayHasKey(0, $decodedResponse);
            $this->assertNotEmpty($decodedResponse[0]);
            $this->assertArrayHasKey('content', $decodedResponse[0]);
            $this->assertArrayHasKey('userNickname', $decodedResponse[0]);
            $this->assertNotNull($decodedResponse[0]['content']);
            $this->assertEquals($testUser->getNickname(), $decodedResponse[0]['userNickname']);
        } else if (!$createFakeReview) {
            $this->assertEmpty($decodedResponse);
        }
    }

    /**
     *  @dataProvider changeGameReviewDataProvider
     */
    public function testChangeGameReviewContent($reviewContent, $createTestReview)
    {
        $testUser = $this->createUser();
        $this->em->persist($testUser);
        $this->em->flush();
        $testToken = $this->addTokenToUser($testUser);

        $testGame = $this->createGame();
        $this->em->persist($testGame);

        if ($createTestReview) {
            $testReview = $this->createReview(
                user: $testUser,
                game: $testGame
            );
            $this->em->persist($testReview);
        }

        $this->em->flush();

        $this->client->jsonRequest(
            'PATCH',
            'https://localhost/api/games/'.$testGame->getId().'/review',
            [
                'reviewId' => isset($testReview) ? $testReview->getId() : 999,
                'content' => $reviewContent
            ],
            [
                'HTTP_Authorization' => 'Bearer '.$testToken
            ]
        );

        $response = $this->client->getResponse();
        $decodedResponse = json_decode($response->getContent(), true);

        if ($reviewContent != null && $createTestReview) {
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
        } else if ($reviewContent != null && !$createTestReview) {
            $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
            $this->assertNotEmpty($decodedResponse['message']);
            $this->assertEquals('Review not found', $decodedResponse['message']);
        } else if ($reviewContent == null && $createTestReview) {
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
        $testUser = $this->createUser();
        $this->em->persist($testUser);
        $this->em->flush();
        $testToken = $this->addTokenToUser($testUser);

        $testGame = $this->createGame();

        $this->em->persist($testGame);

        if ($createFakeReview) {
            $testReview = $this->createReview(
                user: $testUser,
                game: $testGame
            );
            $this->em->persist($testReview);
        }

        $this->em->flush();

        $this->client->jsonRequest(
            'DELETE',
            'https://localhost/api/games/'.$testGame->getId().'/review',
            [
                'reviewId' => isset($testReview) ? $testReview->getId() : 999
            ],
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
            $this->assertEquals('Review not found', $decodedResponse['message']);
        }
    }

    /**
     *  @dataProvider getUserReviewContentByGameIdDataProvider
     */
    public function testGetUserReviewContentByGameId($createFakeReview)
    {
        $testUser = $this->createUser();
        $this->em->persist($testUser);
        $this->em->flush();
        $testToken = $this->addTokenToUser($testUser);

        $testGame = $this->createGame();
        $this->em->persist($testGame);

        if ($createFakeReview) {
            $testReview = $this->createReview(
                user: $testUser,
                game: $testGame
            );
            $this->em->persist($testReview);
        }

        $this->em->flush();

        $this->client->jsonRequest(
            'GET',
            'https://localhost/api/games/'.$testGame->getId().'/review',
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
            $this->assertArrayHasKey('reviewId', $decodedResponse);
            $this->assertArrayHasKey('reviewContent', $decodedResponse);
            $this->assertNotNull($decodedResponse['reviewId']);
            $this->assertNotNull($decodedResponse['reviewContent']);
            $this->assertEquals($testReview->getId(), $decodedResponse['reviewId']);
            $this->assertEquals($testReview->getContent(), $decodedResponse['reviewContent']);
        } else if (!$createFakeReview) {
            $this->assertNotEmpty($decodedResponse);
            $this->assertArrayHasKey('reviewId', $decodedResponse);
            $this->assertArrayHasKey('reviewContent', $decodedResponse);
            $this->assertEmpty($decodedResponse['reviewId']);
            $this->assertEmpty($decodedResponse['reviewContent']);
        }
    }
}