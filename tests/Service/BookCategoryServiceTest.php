<?php

namespace Service;

use App\Tests\AbstractTestCase;
use App\Entity\BookCategory;
use App\Model\BookCategory as BookCategoryModel;
use App\Model\BookCategoryListResponse;
use App\Repository\BookCategoryRepository;
use App\Service\BookCategoryService;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

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

        $slugger = $this->createMock(SluggerInterface::class);
        $em = $this->createMock(EntityManagerInterface::class);

        $service = new BookCategoryService($em, $slugger, $repository);

        $expected = new BookCategoryListResponse([new BookCategoryModel(1, 'Test', 'test')]);

        $this->assertEquals($expected, $service->getCategories());
    }
}
