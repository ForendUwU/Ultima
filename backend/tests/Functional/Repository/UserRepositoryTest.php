<?php

namespace App\Tests\Functional\Repository;

use App\Entity\User;
use App\Tests\Functional\Repository\FakeUserClass\FakeUser;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserRepositoryTest extends WebTestCase
{
    use ResetDatabase;

    private EntityManager $em;

    protected function setUp(): void
    {
        $client = static::createClient();

        $this->em = $client->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @throws NotSupported
     */
    public function testUpgradePasswordSuccess(): void
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

    /**
     * @throws NotSupported
     */
    public function testUpgradePasswordNotSupported(): void
    {
        $testUser = new FakeUser();
        $newPassword = 'new test password';

        $userRepository = $this->em->getRepository(User::class);

        $this->expectException(UnsupportedUserException::class);
        $userRepository->upgradePassword($testUser, $newPassword);
    }
}
