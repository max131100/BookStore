<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < 1000; $i++) {
            $user = (new User())
                ->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setRoles([rand(0, 1) > 0 ? 'ROLE_AUTHOR' : 'ROLE_USER'])
                ->setEmail($faker->unique()->safeEmail());
            $user->setPassword($this->passwordHasher->hashPassword($user, '1111'));

            $manager->persist($user);
            $manager->flush();
            $manager->clear();
            echo "Processed {$i}...";
        }
    }

}
