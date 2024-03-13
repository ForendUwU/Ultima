<?php

namespace App\Tests\Unit\Service;

use App\Entity\Game;
use App\Entity\PurchasedGame;
use App\Entity\Review;
use App\Entity\User;
use App\Repository\PurchasedGameRepository;
use App\Repository\ReviewRepository;
use App\Service\GetEntitiesService;
use App\Service\PlayingService;
use App\Service\ReviewsService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ReviewsServiceTest extends TestCase
{
    private $emMock;
    private $getEntitiesServiceMock;
    private ReviewsService $reviewsService;
    public function setUp(): void
    {
        $this->emMock = $this->createMock(EntityManagerInterface::class);
        $this->getEntitiesServiceMock = $this->createMock(GetEntitiesService::class);
        $this->reviewsService = new ReviewsService(
            $this->emMock,
            $this->getEntitiesServiceMock
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
        $testGame = new Game();

        $testUser = new User();
        $testUser->setLogin('testLogin');

        $this->getEntitiesServiceMock
            ->expects($this->once())
            ->method('getUserByLogin')
            ->willReturn($testUser);
        $this->getEntitiesServiceMock
            ->expects($this->once())
            ->method('getGameById')
            ->willReturn($testGame);

        $fakeReview = null;

        if ($reviewAlreadyExists) {
            $fakeReview = new Review();
            $fakeReview->setGame($testGame);
            $fakeReview->setUser($testUser);
            $fakeReview->setLikes(0);
            $fakeReview->setDislikes(0);
            $fakeReview->setContent('test content');
        }

        $reviewRepositoryMock = $this->createMock(ReviewRepository::class);
        $this->emMock
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($reviewRepositoryMock);
        $reviewRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($fakeReview);

        $expectedReview = new Review();
        $expectedReview->setGame($testGame);
        $expectedReview->setUser($testUser);
        $expectedReview->setLikes(0);
        $expectedReview->setDislikes(0);
        $expectedReview->setContent('test content');

        if (!$reviewAlreadyExists) {
            $result = $this->reviewsService->createGameReview('test content', $testUser, $testGame);

            $this->assertEquals($expectedReview, $result);
        } else if ($reviewAlreadyExists) {
            $this->expectException(\Exception::class);
            $this->expectExceptionMessage('User already has review on this game');

            $this->reviewsService->createGameReview('test content', $testUser, $testGame);
        }

    }

    public function testGetGameReviews()
    {
        $testGame = new Game();

        $testUser = new User();
        $testUser->setLogin('testLogin');
        $testUser->setNickname('testNickname');

        $this->getEntitiesServiceMock
            ->expects($this->once())
            ->method('getGameById')
            ->willReturn($testGame);

        $testReview1 = new Review();
        $testReview1->setGame($testGame);
        $testReview1->setUser($testUser);
        $testReview1->setLikes(0);
        $testReview1->setDislikes(0);
        $testReview1->setContent('test content');

        $testReview2 = new Review();
        $testReview2->setGame($testGame);
        $testReview2->setUser($testUser);
        $testReview2->setLikes(0);
        $testReview2->setDislikes(0);
        $testReview2->setContent('test content');

        $reviewRepositoryMock = $this->createMock(ReviewRepository::class);
        $this->emMock
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($reviewRepositoryMock);
        $reviewRepositoryMock
            ->expects($this->once())
            ->method('findBy')
            ->willReturn([$testReview1, $testReview2]);

        $result = $this->reviewsService->getGameReviews(1);

        $expectedData = [
            'content' => 'test content',
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
        $testGame = new Game();

        $testUser = new User();
        $testUser->setLogin('testLogin');
        $testUser->setNickname('testNickname');

        $this->getEntitiesServiceMock
            ->expects($this->once())
            ->method('getUserByLogin')
            ->willReturn($testUser);
        $this->getEntitiesServiceMock
            ->expects($this->once())
            ->method('getGameById')
            ->willReturn($testGame);

        $testReview = new Review();
        $testReview->setGame($testGame);
        $testReview->setUser($testUser);
        $testReview->setLikes(0);
        $testReview->setDislikes(0);
        $testReview->setContent('test content');

        $reviewRepositoryMock = $this->createMock(ReviewRepository::class);
        $this->emMock
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($reviewRepositoryMock);
        $reviewRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($testReview);

        $result = $this->reviewsService->changeGameReviewContent('Changed content', 'testLogin', $testGame->getId());

        $expectedReview = new Review();
        $expectedReview->setContent('Changed content');
        $expectedReview->setLikes(0);
        $expectedReview->setDislikes(0);
        $expectedReview->setUser($testUser);
        $expectedReview->setGame($testGame);

        $this->assertNotNull($result);
        $this->assertEquals($expectedReview, $result);
    }

    public function testDeleteUsersReviewContent()
    {
        $testGame = new Game();

        $testUser = new User();
        $testUser->setLogin('testLogin');
        $testUser->setNickname('testNickname');

        $this->getEntitiesServiceMock
            ->expects($this->once())
            ->method('getUserByLogin')
            ->willReturn($testUser);
        $this->getEntitiesServiceMock
            ->expects($this->once())
            ->method('getGameById')
            ->willReturn($testGame);

        $testReview = new Review();
        $testReview->setGame($testGame);
        $testReview->setUser($testUser);
        $testReview->setLikes(0);
        $testReview->setDislikes(0);
        $testReview->setContent('test content');

        $reviewRepositoryMock = $this->createMock(ReviewRepository::class);
        $this->emMock
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($reviewRepositoryMock);
        $reviewRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($testReview);

        $this->emMock
            ->expects($this->once())
            ->method('remove');

        $this->reviewsService->deleteUsersReview('testLogin', $testGame->getId());
    }

    /**
     *  @dataProvider getUserReviewContentByUserLoginAndGameIdDataProvider
     */
    public function testGetUserReviewContentByUserLoginAndGameId1($reviewExists)
    {
        $testGame = new Game();

        $testUser = new User();
        $testUser->setLogin('testLogin');
        $testUser->setNickname('testNickname');

        $this->getEntitiesServiceMock
            ->expects($this->once())
            ->method('getUserByLogin')
            ->willReturn($testUser);
        $this->getEntitiesServiceMock
            ->expects($this->once())
            ->method('getGameById')
            ->willReturn($testGame);

        $testReview = null;

        if ($reviewExists) {
            $testReview = new Review();
            $testReview->setGame($testGame);
            $testReview->setUser($testUser);
            $testReview->setLikes(0);
            $testReview->setDislikes(0);
            $testReview->setContent('test content');
        }

        $reviewRepositoryMock = $this->createMock(ReviewRepository::class);
        $this->emMock
            ->expects($this->once())
            ->method('getRepository')
            ->willReturn($reviewRepositoryMock);
        $reviewRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($testReview);

        if ($reviewExists) {
            $result = $this->reviewsService->getUserReviewContentByUserLoginAndGameId('testLogin', $testGame->getId());

            $this->assertEquals('test content', $result);
        } else if (!$reviewExists) {
            $result = $this->reviewsService->getUserReviewContentByUserLoginAndGameId('testLogin', $testGame->getId());

            $this->assertNull($result);
        }
    }
}