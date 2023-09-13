<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\BookChapter;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class BookChapterFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $books = $manager->getRepository(Book::class)->findAll();
        foreach ($books as $book) {
            for ($i = 1; $i <= rand(5, 10); $i++) {
                $chapter = (new BookChapter())
                    ->setTitle("CHAPTER-{$i}")
                    ->setSlug("CHAPTER-{$i}")
                    ->setBook($book)
                    ->setSort($i)
                    ->setLevel(1)
                    ->setParent(null);

                $manager->persist($chapter);

                for ($j = 1; $j <= rand(5, 8); $j++) {
                    $childChapter = (new BookChapter())
                        ->setTitle("CHAPTER-{$i}-{$j}")
                        ->setSlug("CHAPTER-{$i}-{$j}")
                        ->setBook($book)
                        ->setSort($j)
                        ->setLevel(2)
                        ->setParent($chapter);

                    $manager->persist($childChapter);

                    for ($k = 1; $k <= rand(3, 5); $k++) {
                        $grandchildChapter = (new BookChapter())
                            ->setTitle("CHAPTER-{$i}-{$j}-{$k}")
                            ->setSlug("CHAPTER-{$i}-{$j}-{$k}")
                            ->setBook($book)
                            ->setSort($k)
                            ->setLevel(3)
                            ->setParent($childChapter);

                        $manager->persist($grandchildChapter);
                    }
                }
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
