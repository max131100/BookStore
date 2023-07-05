<?php

namespace Service;

use App\Entity\BookCategory;
use App\Model\BookCategoryListItem;
use App\Model\BookCategoryListResponse;
use App\Repository\BookCategoryRepository;
use App\Service\BookCategoryService;
use Doctrine\Common\Collections\Criteria;
use PHPUnit\Framework\TestCase;

class BookCategoryServiceTest extends TestCase
{

    public function testGetCategories(): void
    {
        $repository = $this->createMock(BookCategoryRepository::class);
        $repository->expects($this->once())
            ->method('findBy')
            ->with([], ['title' => Criteria::ASC])
            ->willReturn([(new BookCategory())->setTitle('Test')->setSlug('test')->setId(1)]);

        $service = new BookCategoryService($repository);

        $expected = new BookCategoryListResponse([new BookCategoryListItem(1, 'Test', 'test')]);

        $this->assertEquals($expected, $service->getCategories());
    }
}
