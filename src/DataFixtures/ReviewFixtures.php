<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Review;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ReviewFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $books = $manager->getRepository(Book::class)->findAll();
        $faker = Factory::create();
        foreach ($books as $book) {
            for ($i = 0; $i < rand(0, 100); $i++) {
                $review = (new Review())
                    ->setBook($book)
                    ->setRating(rand(1, 5))
                    ->setContent($faker->paragraph())
                    ->setCreatedAt($faker->dateTimeBetween('-5 years', 'now'))
                    ->setAuthor($faker->name());

                $manager->persist($review);
            }

            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            BookFixtures::class
        ];
    }

}
