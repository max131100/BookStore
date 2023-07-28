<?php

namespace App\Tests\Repository;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Repository\BookRepository;
use App\Tests\AbstractRepositoryTest;
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
        $testCategory = (new BookCategory())->setTitle('Test category')->setSlug('test-category');

        $this->em->persist($testCategory);

        for ($i = 0; $i < 5; ++$i) {
            $book = $this->createBook('test'.$i, $testCategory);
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
