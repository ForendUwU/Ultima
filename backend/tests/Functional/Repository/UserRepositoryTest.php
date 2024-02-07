<?php

namespace App\Tests\Functional\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    private EntityManager $em;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }
    public function testUpgradePassword(): void
    {
        $oldPassword = 'test password';
        $newPassword = 'new test password';

        $testUser = new User();
        $testUser->setLogin('test login');
        $testUser->setNickname('test nickname');
        $testUser->setEmail('test@email.com');
        $testUser->setPassword($oldPassword);

        $userRepository = $this->em->getRepository(User::class);
        $userRepository->upgradePassword($testUser, $newPassword);

        $this->assertEquals($newPassword, $testUser->getPassword());
    }
}