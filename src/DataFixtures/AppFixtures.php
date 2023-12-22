<?php

namespace App\DataFixtures;

use App\Factory\GameFactory;
use App\Factory\PurchasedGameFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        GameFactory::createMany(10);
        UserFactory::createMany(10);
        PurchasedGameFactory::createMany(10);
        $manager->flush();
    }
}
