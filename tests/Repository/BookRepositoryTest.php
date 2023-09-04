<?php

namespace App\Tests\Repository;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Repository\BookRepository;
use App\Tests\AbstractRepositoryTest;
use App\Tests\MockUtils;
use Doctrine\Common\Collections\ArrayCollection;

class BookRepositoryTest extends AbstractRepositoryTest
{
    private BookRepository $bookRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->bookRepository = $this->getRepositoryForEntity(Book::class);
    }

    public function testFindBooksByCategoryId()
    {
        $user = MockUtils::createUser();
        $this->em->persist($user);

        $testCategory = MockUtils::createBookCategory();

        $this->em->persist($testCategory);

        for ($i = 0; $i < 5; ++$i) {
            $book = MockUtils::createBook()->setUser($user)
                ->setCategories(new ArrayCollection([$testCategory]))
                ->setTitle('device-'.$i);

            $this->em->persist($book);
        }

        $this->em->flush();

        $this->assertCount(5, $this->bookRepository->findPublishedBooksByCategoryId($testCategory->getId()));
    }

    private function createBook(string $title, BookCategory $category): Book
    {
        return (new Book())
            ->setTitle($title)
            ->setSlug($title)
            ->setMeap(false)
            ->setIsbn('123321')
            ->setDescription('Test description')
            ->setAuthors(['author'])
            ->setPublicationDate(new \DateTime())
            ->setCategories(new ArrayCollection([$category]))
            ->setImage('http://localhost/'.$title.'.png');
    }
}
