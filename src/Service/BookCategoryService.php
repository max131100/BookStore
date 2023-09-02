<?php

namespace App\Service;

use App\Entity\BookCategory;
use App\Exception\BookCategoryAlreadyExistsException;
use App\Exception\BookCategoryNotEmptyException;
use App\Model\BookCategory as BookCategoryModel;
use App\Model\BookCategoryListResponse;
use App\Model\BookCategoryUpdateRequest;
use App\Model\IdResponse;
use App\Repository\BookCategoryRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class BookCategoryService
{
    public function __construct(
        private EntityManagerInterface $em,
        private SluggerInterface $slugger,
        private BookCategoryRepository $repository)
    {
    }

    public function deleteCategory(int $id): void
    {
        $category = $this->repository->getById($id);
        $booksCount = $this->repository->countBooksInCategory($category->getId());
        if ($booksCount > 0) {
            throw new BookCategoryNotEmptyException($booksCount);
        }

        $this->repository->remove($category, true);
    }

    public function createCategory(BookCategoryUpdateRequest $updateRequest): IdResponse
    {
        $category = new BookCategory();

        $this->upsertCategory($category, $updateRequest);

        return new IdResponse($category->getId());
    }

    public function updateCategory(int $id, BookCategoryUpdateRequest $updateRequest): void
    {
        $this->upsertCategory($this->repository->getById($id), $updateRequest);
    }

    public function getCategories(): BookCategoryListResponse
    {
        $categories = $this->repository->findAllSortedByTitle();
        $items = array_map(
            fn(BookCategory $bookCategory) => new BookCategoryModel(
                $bookCategory->getId(), $bookCategory->getTitle(), $bookCategory->getSlug()
            ),
            $categories
        );
        return new BookCategoryListResponse($items);
    }

    private function upsertCategory(BookCategory $category, BookCategoryUpdateRequest $updateRequest):void
    {
        $slug = $this->slugger->slug($updateRequest->getTitle());

        if ($this->repository->existsBySlug($slug)) {
            throw new BookCategoryAlreadyExistsException();
        }

        $category->setTitle($updateRequest->getTitle())->setSlug($slug);

        $this->em->persist($category);
        $this->em->flush();
    }
}
