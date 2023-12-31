<?php

namespace App\Repository;

use App\Entity\Book;
use App\Entity\BookToBookFormat;
use App\Exception\BookNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }


    public function remove(Book $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function saveBookFormatReference(BookToBookFormat $bookToBookFormat): void
    {
        $this->_em->persist($bookToBookFormat);
    }

    public function removeBookFormatReference(BookToBookFormat $bookToBookFormat): void
    {
        $this->_em->remove($bookToBookFormat);
    }

    /**
     * @param int $categoryId
     * @return Book[]
     */
    public function findPublishedBooksByCategoryId(int $categoryId): array
    {
        return $this->_em
            ->createQuery('SELECT b FROM App\Entity\Book b WHERE :categoryId MEMBER OF b.categories AND b.publicationDate IS NOT NULL')
            ->setParameter('categoryId', $categoryId)
            ->getResult();
    }

    public function getPublishedById(int $id): Book
    {
        $book = $this->_em->createQuery('SELECT b FROM App\Entity\Book b WHERE b.id = :id AND b.publicationDate IS NOT NULL')
            ->setParameter('id', $id)
            ->getOneOrNullResult();

        if ($book === null) {
            throw new BookNotFoundException();
        }

        return $book;
    }

    /**
     * @return Book[]
     */
    public function findBooksByIds(array $ids): array
    {
        return $this->_em->createQuery('SELECT b FROM App\Entity\Book b WHERE b.id IN (:ids) and b.publicationDate IS NOT NULL')
            ->setParameter('ids', $ids)
            ->getResult();
    }

    /**
     * @return Book[]
     */
    public function findUserBooks(UserInterface $user): array
    {
        return $this->findBy(['user' => $user]);
    }

    public function getBookById(int $id): Book
    {
        $book = $this->find($id);

        if ($book === null) {
            throw new BookNotFoundException();
        }

        return $book;
    }

    public function existsBySlug(string $slug): bool
    {
        return $this->findOneBy(['slug' => $slug]) !== null;
    }

    public function existsUserBookById(int $id, UserInterface $user): bool
    {
        return $this->findOneBy(['id' => $id, 'user' => $user]) !== null;
    }
}
