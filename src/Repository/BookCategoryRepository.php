<?php

namespace App\Repository;

use App\Entity\BookCategory;
use App\Exception\BookCategoryNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BookCategory>
 *
 * @method BookCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookCategory[]    findAll()
 * @method BookCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookCategory::class);
    }

    public function remove(BookCategory $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return BookCategory[]
     */
    public function findAllSortedByTitle(): array
    {
        return $this->findBy([], ['title' => Criteria::ASC]);
    }

    public function existsById(int $id): bool
    {
        return $this->find($id) !== null;
    }

    public function getById(int $id): BookCategory
    {
        $category = $this->find($id);

        if ($category === null) {
            throw new BookCategoryNotFoundException();
        }

        return $category;
    }

    public function countBooksInCategory(int $categoryId): int
    {
        return $this->_em->createQuery('SELECT COUNT(b.id) FROM App\Entity\Book b WHERE :categoryId MEMBER OF b.categories')
            ->setParameter('categoryId', $categoryId)
            ->getSingleScalarResult();
    }

    public function existsBySlug(string $slug): bool
    {
        return $this->findOneBy(['slug' => $slug]) !== null;
    }
}
