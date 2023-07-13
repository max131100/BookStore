<?php

namespace Service;

use App\Repository\ReviewRepository;
use App\Tests\AbstractTestCase;
use App\Entity\Book;
use App\Entity\BookCategory;
use App\Exception\BookCategoryNotFoundException;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;
use App\Service\BookService;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;

class BookServiceTest extends AbstractTestCase
{

    public function testGetBooksByCategoryNotFound(): void
    {
        $reviewRepository = $this->createMock(ReviewRepository::class);
        $bookRepository = $this->createMock(BookRepository::class);

        $bookCategoryRepository = $this->createMock(BookCategoryRepository::class);
        $bookCategoryRepository->expects($this->once())
            ->method('existsById')
            ->with(130)
            ->willReturn(false);

        $this->expectException(BookCategoryNotFoundException::class);

        (new BookService($bookRepository, $bookCategoryRepository, $reviewRepository))->getBooksByCategory(130);
    }

    public function testGetBooksByCategory(): void
    {
        $reviewRepository = $this->createMock(ReviewRepository::class);
        $bookRepository = $this->createMock(BookRepository::class);
        $bookRepository->expects($this->once())
            ->method('findBooksByCategoryId')
            ->with(130)
            ->willReturn([$this->createBookEntity()]);

        $bookCategoryRepository = $this->createMock(BookCategoryRepository::class);
        $bookCategoryRepository->expects($this->once())
            ->method('existsById')
            ->with(130)
            ->willReturn(true);

        $service = new BookService($bookRepository, $bookCategoryRepository, $reviewRepository);

        $expected = new BookListResponse([$this->createItemModel()]);

        $this->assertEquals($expected, $service->getBooksByCategory(130));
    }

    public function createBookEntity(): Book
    {
        $book = (new Book())
            ->setTitle('Test book')
            ->setSlug('test book')
            ->setMeap(false)
            ->setIsbn('123321')
            ->setDescription('Test description')
            ->setAuthors(['Tester'])
            ->setCategories(new ArrayCollection())
            ->setPublicationDate(new DateTime('2020-10-10'))
            ->setImage('test image');

        $this->setEntityId($book, 123);

        return $book;
    }

    private function createItemModel(): BookListItem
    {
        return (new BookListItem())
            ->setId(123)
            ->setTitle('Test book')
            ->setSlug('test book')
            ->setMeap(false)
            ->setAuthors(['Tester'])
            ->setPublicationDate(1602277200)
            ->setImage('test image');
    }
}
