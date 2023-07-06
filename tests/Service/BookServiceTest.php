<?php

namespace Service;

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
use PHPUnit\Framework\TestCase;

class BookServiceTest extends TestCase
{

    public function testGetBooksByCategoryNotFound(): void
    {
        $bookRepository = $this->createMock(BookRepository::class);

        $bookCategoryRepository = $this->createMock(BookCategoryRepository::class);
        $bookCategoryRepository->expects($this->once())
            ->method('find')
            ->with(130)
            ->willReturn(null);

        $this->expectException(BookCategoryNotFoundException::class);

        (new BookService($bookRepository, $bookCategoryRepository))->getBooksByCategory(130);
    }

    public function testGetBooksByCategory(): void
    {
        $bookRepository = $this->createMock(BookRepository::class);
        $bookRepository->expects($this->once())
            ->method('findBooksByCategoryId')
            ->with(130)
            ->willReturn([$this->createBookEntity()]);

        $bookCategoryRepository = $this->createMock(BookCategoryRepository::class);
        $bookCategoryRepository->expects($this->once())
            ->method('find')
            ->with(130)
            ->willReturn(new BookCategory());

        $service = new BookService($bookRepository, $bookCategoryRepository);

        $expected = new BookListResponse([$this->createItemModel()]);

        $this->assertEquals($expected, $service->getBooksByCategory(130));
    }

    public function createBookEntity(): Book
    {
        return (new Book())
            ->setId(123)
            ->setTitle('Test book')
            ->setSlug('test book')
            ->setMeap(false)
            ->setAuthors(['Tester'])
            ->setCategories(new ArrayCollection())
            ->setPublicationDate(new DateTime('2020-10-10'))
            ->setImage('test image');
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
