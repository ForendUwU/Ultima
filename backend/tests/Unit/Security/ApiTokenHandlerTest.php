<?php

namespace App\Tests\Unit\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\ApiTokenHandler;
use App\Service\TokenService;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\ExpiredException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class ApiTokenHandlerTest extends TestCase
{
    public function getUserBadgeDataProvider(): array
    {
        $testUser = new User();
        $testUser->setLogin('test login');

        $expectedUserBadge = new UserBadge($testUser->getLogin());

        $fakeTokenCreationDate = new \DateTimeImmutable();
        $goodMockedDecodedToken = new StdClass();
        $goodMockedDecodedToken->tokenCreationDate = $fakeTokenCreationDate->format('Y-m-d H:i:s');
        $expiredMockedDecodedToken = new StdClass();
        $expiredMockedDecodedToken->tokenCreationDate = $fakeTokenCreationDate->modify('-1 day')->format('Y-m-d H:i:s');

        return[
            'success' => [$testUser, $goodMockedDecodedToken, $expectedUserBadge],
            'bad credentials' => [null, null, $expectedUserBadge],
            'expired token' => [$testUser, $expiredMockedDecodedToken, $expectedUserBadge]
        ];
    }

    /**
     * @dataProvider getUserBadgeDataProvider
     */
    public function testGetUserBadgeFrom($testUser, $mockedToken, $expectedUserBadge): void
    {
        $testToken = 'Some token';

        $emMock = $this->createMock(EntityManagerInterface::class);
        $repositoryMock = $this->createMock(UserRepository::class);
        $tokenServiceMock = $this->createMock(TokenService::class);

        $repositoryMock->expects($this->once())
            ->method('findOneBy')
            ->willReturn($testUser);
        $emMock->expects($this->once())
            ->method('getRepository')
            ->willReturn($repositoryMock);


        $mockedDecodedToken = new StdClass();
        $fakeTokenCreationDate = new \DateTimeImmutable();
        $mockedDecodedToken->tokenCreationDate = $fakeTokenCreationDate->format('Y-m-d H:i:s');

        $apiTokenHandler = new ApiTokenHandler($emMock, $tokenServiceMock);

        if ($testUser) {
            $tokenServiceMock->expects($this->once())
                ->method('decode')
                ->willReturn($mockedDecodedToken);

            $this->assertEquals($expectedUserBadge, $apiTokenHandler->getUserBadgeFrom($testToken));
        } elseif (empty($testUser)) {
            $this->expectException(BadCredentialsException::class);
            $apiTokenHandler->getUserBadgeFrom($testToken);
        } else {
            $this->expectException(ExpiredException::class);
            $this->expectExceptionMessage('Token expired');

            $apiTokenHandler->getUserBadgeFrom($testToken);
        }
    }
}