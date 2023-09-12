<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\BookChapter;
use App\Model\Author\UpdateBookChapterSortRequest;
use App\Model\BookChapter as BookChapterModel;
use App\Exception\BookChapterInvalidSortException;
use App\Model\Author\CreateBookChapterRequest;
use App\Model\Author\UpdateBookChapterRequest;
use App\Model\BookChapterTreeResponse;
use App\Model\IdResponse;
use App\Repository\BookChapterRepository;
use App\Repository\BookRepository;
use Symfony\Component\String\Slugger\SluggerInterface;

class AuthorBookChapterService
{
    private const MAX_LEVEL = 3;

    private const MIN_LEVEL = 1;

    private const SORT_STEP = 1;

    public function __construct(
        private BookRepository $bookRepository,
        private BookChapterRepository $bookChapterRepository,
        private SluggerInterface $slugger)
    {
    }

    public function createChapter(CreateBookChapterRequest $request, int $bookId): IdResponse
    {
        $book = $this->bookRepository->getBookById($bookId);
        $title = $request->getTitle();
        $parentId = $request->getParentId();
        $parent = null;
        $level = self::MIN_LEVEL;

        if ($parentId !== null) {
            $parent = $this->bookChapterRepository->getBookChapterById($parentId);
            $parentLevel = $parent->getLevel();
            if ($parentLevel === self::MAX_LEVEL) {
                throw new BookChapterInvalidSortException('max level has been reached');
            }

            $level = $parentLevel + 1;
        }

        $chapter = (new BookChapter())
            ->setTitle($title)
            ->setSlug($this->slugger->slug($title))
            ->setParent($parent)
            ->setSort($this->getNextMaxSort($book, $level))
            ->setBook($book);

        $this->bookChapterRepository->save($chapter, true);

        return new IdResponse($chapter->getId());
    }

    public function updateChapter(UpdateBookChapterRequest $request): void
    {
        $chapter = $this->bookChapterRepository->getBookChapterById($request->getId());
        $title = $request->getTitle();
        $chapter->setTitle($title)->setSlug($this->slugger->slug($title));

        $this->bookChapterRepository->save($chapter, true);
    }

    public function deleteChapter(int $id): void
    {
        $chapter = $this->bookChapterRepository->getBookChapterById($id);

        $this->bookChapterRepository->remove($chapter, true);
    }

    public function getChaptersTree(int $bookId): BookChapterTreeResponse
    {
        $book = $this->bookRepository->getBookById($bookId);
        $chapters = $this->bookChapterRepository->findSortedChaptersByBook($book);
        $response = new BookChapterTreeResponse();
        /** @var array<int, BookChapterModel> $index */
        $index = [];

        foreach ($chapters as $chapter) {
            $model = new BookChapterModel($chapter->getId(), $chapter->getTitle(), $chapter->getSlug());
            $index[$chapter->getId()] = $model;

            if (!$chapter->hasParent()) {
                $response->addItem($model);
                continue;
            }

            $parent = $chapter->getParent();
            $index[$parent->getId()]->addItem($model);
        }

        return $response;
    }

    public function updateChapterSort(UpdateBookChapterSortRequest $request): void
    {
        $chapter = $this->bookChapterRepository->getBookChapterById($request->getId());
        $sortContext = SortContext::fromNeighbours($request->getNextId(), $request->getPreviousId());
        $nearChapter = $this->bookChapterRepository->getBookChapterById($sortContext->getNearId());
        $level = $nearChapter->getLevel();

        if ($sortContext->getPosition() === SortPosition::AsLast) {
            $sort = $this->getNextMaxSort($chapter->getBook(), $level);
        } else {
            $sort = $nearChapter->getSort();
            $this->bookChapterRepository->increaseSortFrom($sort, $chapter->getBook(), $level, self::SORT_STEP);
        }

        $chapter->setLevel($level)->setSort($sort)->setParent($nearChapter->getParent());
        $this->bookChapterRepository->save($chapter, true);
    }

    private function getNextMaxSort(Book $book, int $level): int
    {
        return $this->bookChapterRepository->getMaxSort($book, $level) + self::SORT_STEP;
    }
}
