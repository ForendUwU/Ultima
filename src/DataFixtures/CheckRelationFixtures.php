<?php

namespace App\DataFixtures;

use App\Entity\Game;
use App\Entity\User;
use App\Factory\GameFactory;
use App\Factory\PurchasedGameFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CheckRelationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 3; $i++)
        {
            GameFactory::createOne([
                'title' => 'Game'.$i,
                'description' => 'Description'.$i,
                'price' => $i,
            ]);

            UserFactory::createOne([
                'balance' => 0,
                'login' => 'login'.$i,
                'email' => 'email'.$i.'@email.com',
                'nickname' => 'nickname'.$i,
                'password' => 'password'.$i,
                'roles' => [],
            ]);
        }
        for ($j = 0; $j < 2; $j++)
        {
            PurchasedGameFactory::createOne([
                'user' => $manager->getRepository(User::class)->findOneBy(['login' => 'login0']),
                'game' => $manager->getRepository(Game::class)->findOneBy(['title' => 'Game'.$j]),
            ]);
        }
        $manager->flush();
    }
}
