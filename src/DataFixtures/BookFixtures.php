<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Entity\BookFormat;
use App\Entity\BookToBookFormat;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class BookFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $formats = $manager->getRepository(BookFormat::class)->findAll();
        $categories = $manager->getRepository(BookCategory::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();

        for ($i = 0; $i < 1000; $i++) {
            $title = $faker->unique()->sentence();
            $randomCategories = $faker->randomElements($categories, rand(1, 3));
            $randomUser = $faker->randomElement($users);

            $book = (new Book())
                ->setTitle($title)
                ->setSlug($this->slugger->slug($title))
                ->setDescription($faker->paragraph())
                ->setIsbn($faker->isbn13())
                ->setAuthors([$faker->name(), $faker->name()])
                ->setMeap(false)
                ->setPublicationDate($faker->dateTimeBetween('-5 years', 'now'))
                ->setImage($faker->imageUrl())
                ->setCategories(new ArrayCollection($randomCategories))
                ->setUser($randomUser);

            $bookToBookFormats = array_map(function (BookFormat $format) use ($book, $manager): BookToBookFormat {
                $bookToBookFormat = (new BookToBookFormat())
                    ->setBook($book)
                    ->setFormat($format)
                    ->setPrice((float)(rand(10, 50)))
                    ->setDiscountPercent(rand(5, 10));

                $manager->persist($bookToBookFormat);

                return $bookToBookFormat;
            }, $formats);

            $book->setFormats(new ArrayCollection($bookToBookFormats));

            $manager->persist($book);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            BookCategoryFixtures::class,
            BookFormatFixtures::class,
            UserFixtures::class,
        ];
    }
}
