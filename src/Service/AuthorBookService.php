<?php

namespace App\Service;

use App\Entity\Book;
use App\Entity\BookToBookFormat;
use App\Exception\BookAlreadyExistsException;
use App\Mapper\BookMapper;
use App\Model\Author\BookDetails;
use App\Model\Author\BookFormatOptions;
use App\Model\Author\BookListItem;
use App\Model\Author\BookListResponse;
use App\Model\Author\CreateBookRequest;
use App\Model\Author\UpdateBookRequest;
use App\Model\Author\UploadCoverResponse;
use App\Model\IdResponse;
use App\Repository\BookCategoryRepository;
use App\Repository\BookFormatRepository;
use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AuthorBookService
{
    public function __construct(
        private EntityManagerInterface $em,
        private BookRepository $bookRepository,
        private BookFormatRepository $bookFormatRepository,
        private BookCategoryRepository $bookCategoryRepository,
        private SluggerInterface $slugger,
        private UploadService $uploadService)
    {
    }

    public function uploadCover(int $id, UploadedFile $file): UploadCoverResponse
    {
        $book = $this->bookRepository->getBookById($id);
        $oldImage = $book->getImage();
        $link = $this->uploadService->uploadBookFile($id, $file);

        $book->setImage($link);

        $this->em->flush();

        if ($oldImage !== null) {
            $this->uploadService->deleteBookFile($id, basename($oldImage));
        }

        return new UploadCoverResponse($link);
    }

    public function getBooks(UserInterface $user): BookListResponse
    {
        return new BookListResponse(
            array_map([$this, 'map'], $this->bookRepository->findUserBooks($user))
        );
    }

    public function createBook(CreateBookRequest $request, UserInterface $user): IdResponse
    {
        $slug = $this->slugifyOrThrow($request->getTitle());

        $book = (new Book())
            ->setTitle($request->getTitle())
            ->setSlug($slug)
            ->setMeap(false)
            ->setUser($user);

        $this->em->persist($book);
        $this->em->flush();

        return new IdResponse($book->getId());
    }

    public function getBook(int $id): BookDetails
    {
        $book = $this->bookRepository->getBookById($id);

        $bookDetails = (new BookDetails())
            ->setIsbn($book->getIsbn())
            ->setDescription($book->getDescription())
            ->setFormats(BookMapper::mapFormats($book))
            ->setCategories(BookMapper::mapCategories($book));

        return BookMapper::map($book, $bookDetails);
    }

    public function updateBook(int $id, UpdateBookRequest $request): void
    {
        $book = $this->bookRepository->getBookById($id);
        $title = $request->getTitle();
        if (!empty($title)) {
            $book->setTitle($title)->setSlug($this->slugifyOrThrow($title));
        }

        $formats = array_map(function (BookFormatOptions $options) use ($book): BookToBookFormat {
                $format = (new BookToBookFormat())
                    ->setPrice($options->getPrice())
                    ->setDiscountPercent($options->getDiscountPercent())
                    ->setBook($book)
                    ->setFormat($this->bookFormatRepository->getById($options->getId()));

                $this->bookRepository->saveBookFormatReference($format);

                return $format;
        }, $request->getFormats());

        foreach ($book->getFormats() as $format) {
            $this->bookRepository->removeBookFormatReference($format);
        }

        $book->setAuthors($request->getAuthors())
            ->setIsbn($request->getIsbn())
            ->setDescription($request->getDescription())
            ->setCategories(new ArrayCollection(
                $this->bookCategoryRepository->findBookCategoriesByIds($request->getCategories())
            ))
            ->setFormats(new ArrayCollection($formats));
    }

    public function deleteBook(int $id): void
    {
        $book = $this->bookRepository->getBookById($id);

        $this->bookRepository->remove($book, true);
    }

    private function slugifyOrThrow(string $title): string
    {
        $slug = $this->slugger->slug($title);
        if ($this->bookRepository->existsBySlug($slug)) {
            throw new BookAlreadyExistsException();
        }

        return $slug;
    }

    private function map(Book $book): BookListItem
    {
        return (new BookListItem())
            ->setId($book->getId())
            ->setTitle($book->getTitle())
            ->setSlug($book->getSlug())
            ->setImage($book->getImage());
    }
}
