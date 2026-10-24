<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\DocumentFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne([
            'email' => 'abc@gmail.com',
            'roles' => ['ROLE_USER', 'ROLE_ADMIN'],
        ]);
        UserFactory::createMany(30);

        DocumentFactory::createMany(50);
    }
}
