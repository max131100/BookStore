<?php

namespace Service;

use App\Tests\AbstractTestCase;
use App\Entity\BookCategory;
use App\Model\BookCategoryListItem;
use App\Model\BookCategoryListResponse;
use App\Repository\BookCategoryRepository;
use App\Service\BookCategoryService;
use Doctrine\Common\Collections\Criteria;

class BookCategoryServiceTest extends AbstractTestCase
{

    public function testGetCategories(): void
    {
        $category = (new BookCategory())->setTitle('Test')->setSlug('test');
        $this->setEntityId($category, 1);
        $repository = $this->createMock(BookCategoryRepository::class);
        $repository->expects($this->once())
            ->method('findAllSortedByTitle')
            ->willReturn([$category]);

        $service = new BookCategoryService($repository);

        $expected = new BookCategoryListResponse([new BookCategoryListItem(1, 'Test', 'test')]);

        $this->assertEquals($expected, $service->getCategories());
    }
}
