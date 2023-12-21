<?php

namespace App\DataFixtures;

use App\Factory\GamesFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        GamesFactory::createMany(10);
        UserFactory::createMany(10);
        $manager->flush();
    }
}
