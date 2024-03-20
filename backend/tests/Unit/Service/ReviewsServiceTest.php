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

        $this->setTestUserAsReturnFromRepositoryMock($userRepositoryMock, $testUser);
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
            $result = $this->reviewsService->createGameReview('test content', $testUser->getLogin(), $testGame->getId());

            $this->assertEquals($expectedReview, $result);
        } else if ($reviewAlreadyExists) {
            $this->expectException(\Exception::class);
            $this->expectExceptionMessage('User already has review on this game');

            $this->reviewsService->createGameReview('test content', $testUser->getLogin(), $testGame->getId());
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
            'likes' => 0,
            'dislikes' => 0,
            'userNickname' => 'testNickname'
        ];

        $this->assertNotEmpty($result);
        $this->assertArrayHasKey(0, $result);
        $this->assertArrayHasKey(1, $result);
        $this->assertNotEmpty($result[0]);
        $this->assertNotEmpty($result[1]);
        $this->assertArrayHasKey('content', $result[0]);
        $this->assertArrayHasKey('likes', $result[0]);
        $this->assertArrayHasKey('dislikes', $result[0]);
        $this->assertArrayHasKey('userNickname', $result[0]);
        $this->assertEquals($expectedData, $result[0]);
    }

    public function testChangeGameReviewContent()
    {
        $testGame = $this->createGame();
        $testUser = $this->createUser();

        $userRepositoryMock = $this->createMock(UserRepository::class);
        $gameRepositoryMock = $this->createMock(GamesRepository::class);
        $reviewRepositoryMock = $this->createMock(ReviewRepository::class);

        static::$emMock
            ->expects($this->any())
            ->method('getRepository')
            ->willReturnOnConsecutiveCalls($userRepositoryMock, $gameRepositoryMock, $reviewRepositoryMock);

        $this->setTestUserAsReturnFromRepositoryMock($userRepositoryMock, $testUser);
        $this->setTestGameAsReturnFromRepositoryMock($gameRepositoryMock, $testGame);

        $testReview = $this->createReview($testUser, $testGame);

        $this->setTestReviewAsReturnFromRepositoryMock($reviewRepositoryMock, $testReview);

        $result = $this->reviewsService->changeGameReviewContent('Changed content', 'testLogin', $testGame->getId());

        $expectedReview = $this->createReview($testUser, $testGame, content: 'Changed content');

        $this->assertNotNull($result);
        $this->assertEquals($expectedReview, $result);
    }

    public function testDeleteUsersReviewContent()
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

        $this->setTestUserAsReturnFromRepositoryMock($userRepositoryMock, $testUser);
        $this->setTestGameAsReturnFromRepositoryMock($gameRepositoryMock, $testGame);

        $testReview = $this->createReview($testUser, $testGame);

        $this->setTestReviewAsReturnFromRepositoryMock($reviewRepositoryMock, $testReview);

        static::$emMock
            ->expects($this->once())
            ->method('remove');

        $this->reviewsService->deleteUsersReview('testLogin', $testGame->getId());
    }

    /**
     *  @dataProvider getUserReviewContentByUserLoginAndGameIdDataProvider
     */
    public function testGetUserReviewContentByUserLoginAndGameId1($reviewExists)
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

        $this->setTestUserAsReturnFromRepositoryMock($userRepositoryMock, $testUser);
        $this->setTestGameAsReturnFromRepositoryMock($gameRepositoryMock, $testGame);

        $testReview = null;

        if ($reviewExists)
            $testReview = $this->createReview($testUser, $testGame);

        $reviewRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($testReview);

        if ($reviewExists) {
            $result = $this->reviewsService->getUserReviewContentByUserLoginAndGameId('testLogin', $testGame->getId());

            $this->assertEquals('testContent', $result);
        } else if (!$reviewExists) {
            $result = $this->reviewsService->getUserReviewContentByUserLoginAndGameId('testLogin', $testGame->getId());

            $this->assertNull($result);
        }
    }
}