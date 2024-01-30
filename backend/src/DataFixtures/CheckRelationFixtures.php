<?php

namespace App\DataFixtures;

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

            PurchasedGameFactory::createMany(1, function(){
                return [
                    'user' => UserFactory::random(),
                    'game' => GameFactory::random(),
                ];
            });
        }
        $manager->flush();
    }
}
