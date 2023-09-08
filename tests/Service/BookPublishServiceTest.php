<?php

namespace App\Tests\Service;

use App\Entity\Book;
use App\Model\Author\PublishBookRequest;
use App\Repository\BookRepository;
use App\Service\BookPublishService;
use App\Tests\AbstractTestCase;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class BookPublishServiceTest extends AbstractTestCase
{
    private BookRepository $bookRepository;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookRepository = $this->createMock(BookRepository::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
    }

    public function testPublish(): void
    {
        $book = new Book();
        $datetime = new DateTimeImmutable('2020-10-10');
        $request = new PublishBookRequest();
        $request->setDate($datetime);

        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        $this->em->expects($this->once())
            ->method('flush');

        (new BookPublishService($this->bookRepository, $this->em))->publish(1, $request);

        $this->assertEquals($datetime, $book->getPublicationDate());
    }

    public function testUnpublish(): void
    {
        $book = new Book();
        $book->setPublicationDate(new DateTimeImmutable('2020-10-10'));

        $this->bookRepository->expects($this->once())
            ->method('getBookById')
            ->with(1)
            ->willReturn($book);

        $this->em->expects($this->once())
            ->method('flush');

        (new BookPublishService($this->bookRepository, $this->em))->unpublish(1);

        $this->assertNull($book->getPublicationDate());
    }
}
