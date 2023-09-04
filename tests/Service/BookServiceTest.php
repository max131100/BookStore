<?php

namespace App\Tests\Service;

use App\Entity\BookCategory;
use App\Entity\BookFormat;
use App\Entity\BookToBookFormat;
use App\Model\BookCategory as BookCategoryModel;
use App\Model\BookDetails;
use App\Model\BookFormat as BookFormatModel;
use App\Service\Rating;
use App\Service\RatingService;
use App\Tests\AbstractTestCase;
use App\Entity\Book;
use App\Exception\BookCategoryNotFoundException;
use App\Model\BookListItem;
use App\Model\BookListResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookRepository;
use App\Service\BookService;
use App\Tests\MockUtils;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;

class BookServiceTest extends AbstractTestCase
{
    private BookRepository $bookRepository;

    private BookCategoryRepository $bookCategoryRepository;

    private RatingService $ratingService;

    protected function setUp(): void
    {
        parent::setUp();

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
            ->method('findPublishedBooksByCategoryId')
            ->with(130)
            ->willReturn([$this->createBookEntity()]);

        $this->bookCategoryRepository->expects($this->once())
            ->method('existsById')
            ->with(130)
            ->willReturn(true);

        $expected = new BookListResponse([$this->createItemModel()]);

        $this->assertEquals($expected, $this->createBookService()->getBooksByCategory(130));
    }

    public function testGetBookById(): void
    {
        $this->bookRepository->expects($this->once())
            ->method('getPublishedById')
            ->with(123)
            ->willReturn($this->createBookEntity());

        $this->ratingService->expects($this->once())
            ->method('calcReviewRatingForBook')
            ->with(123)
            ->willReturn(new Rating(10, 5.0));

        $format = (new BookFormatModel())
            ->setId(1)
            ->setTitle('format')
            ->setDescription('description format')
            ->setComment(null)
            ->setPrice(123.55)
            ->setDiscountPercent(5);

        $expected = (new BookDetails())
            ->setId(123)
            ->setTitle('Test book')
            ->setSlug('test-book')
            ->setImage('http://localhost.png')
            ->setMeap(false)
            ->setPublicationDate((new DateTime('2020-10-10'))->getTimestamp())
            ->setAuthors(['Tester'])
            ->setCategories([new BookCategoryModel(1, 'Devices', 'devices')])
            ->setRating(5.0)
            ->setReviews(10)
            ->setFormats([$format]);

        $this->assertEquals($expected, $this->createBookService()->getBookById(123));

    }

    private function createBookService(): BookService
    {
        return new BookService(
            $this->bookRepository,
            $this->bookCategoryRepository,
            $this->ratingService);
    }

    private function createBookEntity(): Book
    {
        $category = MockUtils::createBookCategory();
        $this->setEntityId($category, 1);

        $format = MockUtils::createBookFormat();
        $this->setEntityId($format, 1);

        $book = MockUtils::createBook()->setCategories(new ArrayCollection([$category]));
        $this->setEntityId($book, 123);

        $join = MockUtils::createBookFormatLink($book, $format);
        $this->setEntityId($join, 1);

        $book->setFormats(new ArrayCollection([$join]));

        return $book;
    }

    private function createItemModel(): BookListItem
    {
        return (new BookListItem())
            ->setId(123)
            ->setTitle('Test book')
            ->setSlug('test-book')
            ->setMeap(false)
            ->setAuthors(['Tester'])
            ->setPublicationDate(1602288000)
            ->setImage('http://localhost.png');
    }
}
