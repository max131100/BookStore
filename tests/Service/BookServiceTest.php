<?php

namespace Service;

use App\Repository\ReviewRepository;
use App\Service\RatingService;
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
    private ReviewRepository $reviewRepository;

    private BookRepository $bookRepository;

    private BookCategoryRepository $bookCategoryRepository;

    private RatingService $ratingService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reviewRepository = $this->createMock(ReviewRepository::class);
        $this->bookRepository = $this->createMock(BookRepository::class);
        $this->bookCategoryRepository = $this->createMock(BookCategoryRepository::class);
        $this->ratingService = $this->createMock(RatingService::class);
    }

    public function testGetBooksByCategoryNotFound(): void
    {
        $this->bookCategoryRepository->expects($this->once())
            ->method('existsById')
            ->with(130)
            ->willReturn(false);

        $this->expectException(BookCategoryNotFoundException::class);

        $this->createBookService()->getBooksByCategory(130);
    }

    public function testGetBooksByCategory(): void
    {
        $this->bookRepository->expects($this->once())
            ->method('findBooksByCategoryId')
            ->with(130)
            ->willReturn([$this->createBookEntity()]);

        $this->bookCategoryRepository->expects($this->once())
            ->method('existsById')
            ->with(130)
            ->willReturn(true);

        $expected = new BookListResponse([$this->createItemModel()]);

        $this->assertEquals($expected, $this->createBookService()->getBooksByCategory(130));
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

    private function createBookService(): BookService
    {
        return new BookService(
            $this->bookRepository,
            $this->bookCategoryRepository,
            $this->reviewRepository,
            $this->ratingService);
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
