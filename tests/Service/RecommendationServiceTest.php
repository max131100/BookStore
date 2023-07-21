<?php

namespace App\Tests\Service;

use App\Entity\Book;
use App\Model\RecommendedBook;
use App\Model\RecommendedBookListResponse;
use App\Repository\BookRepository;
use App\Service\Recommendation\Model\RecommendationItem;
use App\Service\Recommendation\Model\RecommendationResponse;
use App\Service\Recommendation\RecommendationApiService;
use App\Service\RecommendationService;
use App\Tests\AbstractTestCase;

class RecommendationServiceTest extends AbstractTestCase
{
    private BookRepository $bookRepository;

    private RecommendationApiService $recommendationApiService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookRepository = $this->createMock(BookRepository::class);
        $this->recommendationApiService = $this->createMock(RecommendationApiService::class);
    }

    public static function dataProvider(): array
    {
        return [
            ['short description', 'short description'],
            [
                <<<EOF
long description long description long description
long description long description long description
long description long description long description
EOF,
                <<<EOF
long description long description long description
long description long description long description
long description long description long descri...
EOF
            ]
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetRecommendationByBookId(string $actualDescription, string $expectedDescription): void
    {
        $entity = (new Book())->setTitle('Book')->setSlug('book')->setImage('image')
            ->setDescription($actualDescription);

        $this->setEntityId($entity, 2);

        $this->bookRepository->expects($this->once())
            ->method('findBooksByIds')
            ->with([2])
            ->willReturn([$entity]);

        $this->recommendationApiService->expects($this->once())
            ->method('getRecommendationsByBookId')
            ->with(1)
            ->willReturn(new RecommendationResponse(1, 124124124, [new RecommendationItem(2)]));

        $expected = new RecommendedBookListResponse([(new RecommendedBook())
                ->setId(2)
                ->setTitle('Book')
                ->setSlug('book')
                ->setImage('image')
                ->setShortDescription($expectedDescription)]);

        $actual = (new RecommendationService($this->bookRepository,
            $this->recommendationApiService))->getRecommendationsByBookId(1);

        $this->assertEquals($expected, $actual);
    }
}
