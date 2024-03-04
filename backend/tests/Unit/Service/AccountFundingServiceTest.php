<?php

namespace App\Tests\Unit\Service;

use App\Entity\User;
use App\Service\AccountFundingService;
use App\Service\GetEntitiesService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class AccountFundingServiceTest extends TestCase
{
    private $emMock;
    private $getEntitiesServiceMock;
    private $accountFuncdingService;
    public function setUp(): void
    {
        $this->emMock = $this->createMock(EntityManagerInterface::class);
        $this->getEntitiesServiceMock = $this->createMock(GetEntitiesService::class);
        $this->accountFuncdingService = new AccountFundingService(
            $this->emMock,
            $this->getEntitiesServiceMock
        );
    }

    public function testFund()
    {
        $testUser = new User();
        $testUser->setLogin('testLogin');

        $this->getEntitiesServiceMock
            ->expects($this->once())
            ->method('getUserByLogin')
            ->willReturn($testUser);

        $this->accountFuncdingService->fund(10, $testUser->getLogin());

        $this->assertEquals('10.00', $testUser->getBalance());
    }
}