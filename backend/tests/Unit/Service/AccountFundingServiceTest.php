<?php

namespace App\Tests\Unit\Service;

use App\Repository\UserRepository;
use App\Service\AccountFundingService;
use App\Tests\Traits\CreateUserTrait;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class AccountFundingServiceTest extends TestCase
{
    use CreateUserTrait;
    public static $emMock;
    private AccountFundingService $accountFuncdingService;

    public function setUp(): void
    {
        static::$emMock = $this->createMock(EntityManagerInterface::class);
        $this->accountFuncdingService = new AccountFundingService(static::$emMock);
    }

    public function testFund()
    {
        $testUser = $this->createUser();

        $userRepositoryMock = $this->createMock(UserRepository::class);
        $this->setUserRepositoryAsReturnFromEntityManager($userRepositoryMock);
        $this->setTestUserAsReturnFromRepositoryMock($userRepositoryMock, $testUser);

        $this->accountFuncdingService->fund(10, $testUser->getLogin());

        $this->assertEquals('10.00', $testUser->getBalance());
    }
}