<?php

namespace App\Tests\Unit\Service;

use App\Entity\Review;
use App\Repository\GamesRepository;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
use App\Service\ReviewsService;
use App\Tests\Traits\CreateGameTrait;
use App\Tests\Traits\CreateReviewTrait;
use App\Tests\Traits\CreateUserTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ReviewsServiceTest extends TestCase
{
    use CreateUserTrait, CreateGameTrait, CreateReviewTrait;

    public static $emMock;
    private ReviewsService $reviewsService;
    public function setUp(): void
    {
        static::$emMock = $this->createMock(EntityManagerInterface::class);
        $this->reviewsService = new ReviewsService(
            static::$emMock
        );
    }

    public function createGameReviewDataProvider(): array
    {
        return [
            'success' => [false],
            'user already has review on this game' => [true]
        ];
    }

    public function getUserReviewContentByUserLoginAndGameIdDataProvider(): array
    {
        return [
            'success' => [true],
            'user doesn\'t have a review' => [false]
        ];
    }

    /**
     *  @dataProvider createGameReviewDataProvider
     */
    public function testCreateGameReview($reviewAlreadyExists)
    {
        $testGame = $this->createGame();
        $testUser = $this->createUser();

        $userRepositoryMock = $this->createMock(UserRepository::class);
        $gameRepositoryMock = $this->createMock(GamesRepository::class);
        $reviewRepositoryMock = $this->createMock(ReviewRepository::class);

        static::$emMock
            ->expects($this->exactly(3))
            ->method('getRepository')
            ->willReturnOnConsecutiveCalls($userRepositoryMock, $gameRepositoryMock, $reviewRepositoryMock);

        $this->setTestUserAsReturnFromRepositoryMockById($userRepositoryMock, $testUser);
        $this->setTestGameAsReturnFromRepositoryMock($gameRepositoryMock, $testGame);

        $fakeReview = null;

        if ($reviewAlreadyExists) {
            $fakeReview = new Review();
            $fakeReview->setGame($testGame);
            $fakeReview->setUser($testUser);
            $fakeReview->setContent('test content');
        }

        $reviewRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($fakeReview);

        $expectedReview = new Review();
        $expectedReview->setGame($testGame);
        $expectedReview->setUser($testUser);
        $expectedReview->setContent('test content');

        if (!$reviewAlreadyExists) {
            $result = $this->reviewsService->createGameReview('test content', $testUser->getId(), $testGame->getId());

            $this->assertEquals($expectedReview, $result);
        } else if ($reviewAlreadyExists) {
            $this->expectException(\Exception::class);
            $this->expectExceptionMessage('User already has review on this game');

            $this->reviewsService->createGameReview('test content', $testUser->getId(), $testGame->getId());
        }

    }

    public function testGetGameReviews()
    {
        $testGame = $this->createGame();
        $testUser = $this->createUser();

        $reviewRepositoryMock = $this->createMock(ReviewRepository::class);
        $gamesRepositoryMock = $this->createMock(GamesRepository::class);

        static::$emMock
            ->expects($this->any())
            ->method('getRepository')
            ->willReturnOnConsecutiveCalls($gamesRepositoryMock, $reviewRepositoryMock);

        $testReview1 = $this->createReview($testUser, $testGame);
        $testReview2 = $this->createReview($testUser, $testGame);

        $reviewRepositoryMock
            ->expects($this->once())
            ->method('findBy')
            ->willReturn([$testReview1, $testReview2]);

        $result = $this->reviewsService->getGameReviews($testGame->getId());

        $expectedData = [
            'content' => 'testContent',
            'userNickname' => 'testNickname'
        ];

        $this->assertNotEmpty($result);
        $this->assertArrayHasKey(0, $result);
        $this->assertArrayHasKey(1, $result);
        $this->assertNotEmpty($result[0]);
        $this->assertNotEmpty($result[1]);
        $this->assertArrayHasKey('id', $result[0]);
        $this->assertArrayHasKey('content', $result[0]);
        $this->assertArrayHasKey('userNickname', $result[0]);
        $this->assertEquals($expectedData['content'], $result[0]['content']);
        $this->assertEquals($expectedData['userNickname'], $result[0]['userNickname']);
    }

    public function testChangeGameReviewContent()
    {
        $testUser = $this->createUser();
        $testGame = $this->createGame();
        $reviewRepositoryMock = $this->createMock(ReviewRepository::class);

        static::$emMock
            ->expects($this->any())
            ->method('getRepository')
            ->willReturnOnConsecutiveCalls($reviewRepositoryMock);

        $testReview = $this->createReview(
            user: $testUser,
            game: $testGame
        );

        $this->setTestReviewAsReturnFromRepositoryMockById($reviewRepositoryMock, $testReview);

        $result = $this->reviewsService->changeGameReviewContent('Changed content', $testUser->getId(), $testGame->getId());

        $expectedReview = $this->createReview($testUser, $testGame, content: 'Changed content');

        $this->assertNotNull($result);
        $this->assertEquals($expectedReview->getId(), $result->getId());
        $this->assertEquals($expectedReview->getUser(), $result->getUser());
        $this->assertEquals($expectedReview->getGame(), $result->getGame());
        $this->assertEquals($expectedReview->getContent(), $result->getContent());
    }

    /**
     *  @dataProvider getUserReviewContentByUserLoginAndGameIdDataProvider
     */
    public function testGetUserReviewByUserLoginAndGameId($reviewExists)
    {
        $testGame = $this->createGame();
        $testUser = $this->createUser();

        $gameRepositoryMock = $this->createMock(GamesRepository::class);
        $userRepositoryMock = $this->createMock(UserRepository::class);
        $reviewRepositoryMock = $this->createMock(ReviewRepository::class);

        static::$emMock
            ->expects($this->any())
            ->method('getRepository')
            ->willReturnOnConsecutiveCalls($userRepositoryMock, $gameRepositoryMock, $reviewRepositoryMock);

        $this->setTestUserAsReturnFromRepositoryMockById($userRepositoryMock, $testUser);
        $this->setTestGameAsReturnFromRepositoryMock($gameRepositoryMock, $testGame);

        $testReview = null;

        if ($reviewExists)
            $testReview = $this->createReview($testUser, $testGame);

        $reviewRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($testReview);

        if ($reviewExists) {
            $result = $this->reviewsService->getUserReview($testUser->getId(), $testGame->getId());

            $this->assertNotNull($result);
            $this->assertArrayHasKey('id', $result);
            $this->assertArrayHasKey('content', $result);
            $this->assertNotNull($result['content']);
            $this->assertEquals('testContent', $result['content']);
        } else if (!$reviewExists) {
            $result = $this->reviewsService->getUserReview($testUser->getId(), $testGame->getId());

            $this->assertNull($result);
        }
    }
}