<?php

namespace App\Tests\Service;

use App\Repository\ReviewRepository;
use App\Service\Rating;
use App\Service\RatingService;
use App\Tests\AbstractTestCase;
use PHPUnit\Framework\TestCase;

class RatingServiceTest extends AbstractTestCase
{
    private ReviewRepository $reviewRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reviewRepository = $this->createMock(ReviewRepository::class);
    }

    public static function provider(): array
    {
        return [
            [25, 20, 1.25],
            [0, 5, 0]
        ];
    }

    /**
     * @dataProvider provider
     */
    public function testCalcReviewRatingForBook(int $repositoryRatingSum, int $total, float $expectedRating): void
    {
        $this->reviewRepository->expects($this->once())
            ->method('getBookTotalRatingSum')
            ->with(1)
            ->willReturn($repositoryRatingSum);

        $this->reviewRepository->expects($this->once())
            ->method('countByBookId')
            ->with(1)
            ->willReturn($total);

        $actual = (new RatingService($this->reviewRepository))->calcReviewRatingForBook(1);

        $this->assertEquals(new Rating($total, $expectedRating), $actual);
    }

    public function testCalcReviewRatingForBookWhenZeroReviews(): void
    {
        $this->reviewRepository->expects($this->never())->method('getBookTotalRatingSum');

        $this->reviewRepository->expects($this->once())
            ->method('countByBookId')
            ->with(1)
            ->willReturn(0);

        $actual = (new RatingService($this->reviewRepository))->calcReviewRatingForBook(1);

        $this->assertEquals(new Rating(0, 0),$actual);
    }
}
