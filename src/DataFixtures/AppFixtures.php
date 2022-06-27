<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setId(1);
        $user->setPassword('admin999#');
        $user->setIsActive(true);
        $user->setEmail('admin@localhost.pl');
        $manager->persist($user);

        $manager->flush();
    }
}
