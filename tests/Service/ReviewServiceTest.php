<?php

namespace App\Tests\Service;

use App\Entity\Review;
use App\Model\Review as ReviewModel;
use App\Model\ReviewPage;
use App\Repository\ReviewRepository;
use App\Service\Rating;
use App\Service\RatingService;
use App\Service\ReviewService;
use App\Tests\AbstractTestCase;
use ArrayIterator;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ReviewServiceTest extends AbstractTestCase
{
    private ReviewRepository $reviewRepository;

    private RatingService $ratingService;

    private const BOOK_ID = 1;

    private const PER_PAGE = 5;

    public static function provider(): array
    {
        return [
            [0, 0],
            [-1, 0],
            [-20, 0]
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->reviewRepository = $this->createMock(ReviewRepository::class);
        $this->ratingService = $this->createMock(RatingService::class);
    }

    /**
     * @dataProvider provider
     */
    public function testGetReviewPageByBookIdInvalidPage(int $page, int $offset): void
    {
        $this->ratingService->expects($this->once())
            ->method('calcReviewRatingForBook')
            ->with(self::BOOK_ID)
            ->willReturn(new Rating(0, 0));

        $this->reviewRepository->expects($this->once())
            ->method('getPageByBookId')
            ->with(self::BOOK_ID, $offset, self::PER_PAGE)
            ->willReturn(new ArrayIterator());

        $service = new ReviewService($this->reviewRepository, $this->ratingService);
        $expected = (new ReviewPage())
            ->setRating(0)
            ->setPages(0)
            ->setPage($page)
            ->setTotal(0)
            ->setPerPage(self::PER_PAGE)
            ->setItems([]);

        $this->assertEquals($expected, $service->getReviewPageByBookId(self::BOOK_ID, $page));
    }

    public function testGetReviewPageByBookId(): void
    {
        $this->ratingService->expects($this->once())
            ->method('calcReviewRatingForBook')
            ->with(self::BOOK_ID)
            ->willReturn(new Rating(1, 4));

        $entity = (new Review())->setAuthor('Tester')->setContent('Test content')
            ->setCreatedAt(new DateTimeImmutable('2020-10-10'))->setRating(4);

        $this->setEntityId($entity, 1);

        $this->reviewRepository->expects($this->once())
            ->method('getPageByBookId')
            ->with(self::BOOK_ID, 0, self::PER_PAGE)
            ->willReturn(new ArrayIterator([$entity]));

        $service = new ReviewService($this->reviewRepository, $this->ratingService);
        $expected = (new ReviewPage())
            ->setRating(4)
            ->setPages(1)
            ->setPage(1)
            ->setTotal(1)
            ->setPerPage(self::PER_PAGE)
            ->setItems([
                (new ReviewModel())->setId(1)->setRating(4)->setContent('Test content')
                    ->setCreatedAt((new DateTimeImmutable('2020-10-10'))->getTimestamp())
                    ->setAuthor('Tester')
            ]);

        $this->assertEquals($expected, $service->getReviewPageByBookId(self::BOOK_ID, 1));
    }
}
